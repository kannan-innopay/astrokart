from datetime import datetime, timedelta

from skyfield.api import load
from skyfield.almanac import find_discrete

from app.services.nakshatra import RASHIS, NAKSHATRAS, get_graha_sanskrit

# Planet names mapping to Skyfield bodies
SKYFIELD_BODIES = {
    "Sun": "sun",
    "Moon": "moon",
    "Mars": "mars",
    "Mercury": "mercury",
    "Jupiter": "jupiter barycenter",
    "Venus": "venus",
    "Saturn": "saturn barycenter",
}

# Lahiri ayanamsa approximate value (for 2026, ~24.17 degrees)
# We compute it more precisely using the standard formula
def _lahiri_ayanamsa(jd: float) -> float:
    """Approximate Lahiri ayanamsa for a given Julian Date."""
    t = (jd - 2451545.0) / 36525.0  # centuries from J2000
    return 23.85 + 0.0137 * (jd - 2451545.0) / 365.25


def get_current_positions(date: datetime | None = None) -> dict:
    """Get current sidereal positions of all 9 grahas."""
    ts = load.timescale()
    eph = load("de421.bsp")
    earth = eph["earth"]

    if date is None:
        date = datetime.utcnow()

    t = ts.utc(date.year, date.month, date.day, date.hour, date.minute)
    jd = t.tt

    ayanamsa = _lahiri_ayanamsa(jd)

    grahas = []

    for name, body_key in SKYFIELD_BODIES.items():
        body = eph[body_key]
        astrometric = earth.at(t).observe(body)
        ra, dec, dist = astrometric.apparent().ecliptic_latlon()

        tropical_lon = ra.degrees if hasattr(ra, "degrees") else float(ra._degrees)
        # For ecliptic, we need ecliptic longitude
        lat, lon, _ = astrometric.apparent().ecliptic_latlon()
        tropical_lon = lon.degrees

        sidereal_lon = (tropical_lon - ayanamsa) % 360
        rashi_index = int(sidereal_lon / 30) % 12
        nak_index = int(sidereal_lon / (13 + 1 / 3)) % 27

        # Speed for retrograde detection (approximate via 1-day difference)
        t2 = ts.utc(date.year, date.month, date.day + 1, date.hour, date.minute)
        astrometric2 = earth.at(t2).observe(body)
        _, lon2, _ = astrometric2.apparent().ecliptic_latlon()
        speed = lon2.degrees - lon.degrees

        grahas.append({
            "name": name,
            "rashi_index": rashi_index,
            "rashi_name": RASHIS[rashi_index]["name"],
            "longitude": round(sidereal_lon, 4),
            "nakshatra": NAKSHATRAS[nak_index],
            "nakshatra_index": nak_index,
            "is_retrograde": speed < 0,
        })

    # Rahu (Mean North Node) — computed from Moon's node
    # Approximate: Rahu moves ~19.36 degrees/year retrograde
    # More precise calculation using Skyfield's moon node
    days_from_j2000 = jd - 2451545.0
    # Mean longitude of ascending node (Rahu)
    rahu_tropical = (125.0445 - 0.0529539 * days_from_j2000) % 360
    rahu_sidereal = (rahu_tropical - ayanamsa) % 360
    rahu_rashi = int(rahu_sidereal / 30) % 12
    rahu_nak = int(rahu_sidereal / (13 + 1 / 3)) % 27

    grahas.append({
        "name": "Rahu",
        "rashi_index": rahu_rashi,
        "rashi_name": RASHIS[rahu_rashi]["name"],
        "longitude": round(rahu_sidereal, 4),
        "nakshatra": NAKSHATRAS[rahu_nak],
        "nakshatra_index": rahu_nak,
        "is_retrograde": True,
    })

    # Ketu is always 180° from Rahu
    ketu_sidereal = (rahu_sidereal + 180) % 360
    ketu_rashi = int(ketu_sidereal / 30) % 12
    ketu_nak = int(ketu_sidereal / (13 + 1 / 3)) % 27

    grahas.append({
        "name": "Ketu",
        "rashi_index": ketu_rashi,
        "rashi_name": RASHIS[ketu_rashi]["name"],
        "longitude": round(ketu_sidereal, 4),
        "nakshatra": NAKSHATRAS[ketu_nak],
        "nakshatra_index": ketu_nak,
        "is_retrograde": True,
    })

    return {
        "date": date.strftime("%Y-%m-%d"),
        "grahas": grahas,
    }


def get_upcoming_transits(months: int = 12) -> list[dict]:
    """Find upcoming sign ingress events for slow-moving planets."""
    now = datetime.utcnow()
    events = []

    # Only track slow planets: Jupiter, Saturn, Rahu, Ketu
    slow_planets = ["Jupiter", "Saturn", "Rahu", "Ketu"]

    # Get current positions
    current = get_current_positions(now)
    current_signs = {g["name"]: g["rashi_index"] for g in current["grahas"]}

    # Scan month by month
    for month_offset in range(1, months + 1):
        future_date = now + timedelta(days=30 * month_offset)
        future = get_current_positions(future_date)

        for graha in future["grahas"]:
            if graha["name"] not in slow_planets:
                continue

            current_sign = current_signs.get(graha["name"])
            if current_sign is not None and current_sign != graha["rashi_index"]:
                # Sign change detected — find approximate date
                ingress_date = _find_ingress_date(
                    graha["name"], current_sign, graha["rashi_index"],
                    now + timedelta(days=30 * (month_offset - 1)),
                    future_date,
                )

                significance = None
                if graha["name"] == "Jupiter":
                    # Jupiter exalted in Cancer (index 3), debilitated in Capricorn (index 9)
                    if graha["rashi_index"] == 3:
                        significance = "exaltation"
                    elif graha["rashi_index"] == 9:
                        significance = "debilitation"
                elif graha["name"] == "Saturn":
                    # Saturn exalted in Libra (6), debilitated in Aries (0)
                    if graha["rashi_index"] == 6:
                        significance = "exaltation"
                    elif graha["rashi_index"] == 0:
                        significance = "debilitation"

                events.append({
                    "planet": graha["name"],
                    "from_rashi_index": current_sign,
                    "from_rashi": RASHIS[current_sign]["name"],
                    "to_rashi_index": graha["rashi_index"],
                    "to_rashi": RASHIS[graha["rashi_index"]]["name"],
                    "date": ingress_date.strftime("%Y-%m-%d"),
                    "significance": significance,
                })

                # Update current sign for next iteration
                current_signs[graha["name"]] = graha["rashi_index"]

    # Deduplicate and sort by date
    seen = set()
    unique_events = []
    for e in sorted(events, key=lambda x: x["date"]):
        key = f"{e['planet']}_{e['to_rashi_index']}_{e['date']}"
        if key not in seen:
            seen.add(key)
            unique_events.append(e)

    return unique_events


def _find_ingress_date(
    planet: str, from_sign: int, to_sign: int,
    start: datetime, end: datetime,
) -> datetime:
    """Binary search to find approximate date of sign change."""
    for _ in range(15):  # ~15 iterations gives day-level precision
        mid = start + (end - start) / 2
        positions = get_current_positions(mid)
        mid_sign = next(
            (g["rashi_index"] for g in positions["grahas"] if g["name"] == planet),
            from_sign,
        )
        if mid_sign == from_sign:
            start = mid
        else:
            end = mid

    return end


def get_forecast(moon_rashi_index: int, lagna_rashi_index: int) -> dict:
    """Get personalized transit forecast based on Moon and Lagna signs."""
    current = get_current_positions()

    # Major planets for forecast
    major_planets = ["Jupiter", "Saturn", "Rahu", "Ketu"]

    transits = []
    saturn_rashi = None

    for graha in current["grahas"]:
        if graha["name"] not in major_planets:
            continue

        house_from_moon = (graha["rashi_index"] - moon_rashi_index) % 12 + 1
        house_from_lagna = (graha["rashi_index"] - lagna_rashi_index) % 12 + 1

        transits.append({
            "planet": graha["name"],
            "rashi_index": graha["rashi_index"],
            "house_from_moon": house_from_moon,
            "house_from_lagna": house_from_lagna,
            "is_retrograde": graha["is_retrograde"],
        })

        if graha["name"] == "Saturn":
            saturn_rashi = graha["rashi_index"]

    # Sade Sati detection
    sade_sati = {"active": False, "phase": None}
    if saturn_rashi is not None:
        saturn_from_moon = (saturn_rashi - moon_rashi_index) % 12
        if saturn_from_moon == 11:  # 12th house (0-indexed: 11)
            sade_sati = {"active": True, "phase": "rising"}
        elif saturn_from_moon == 0:  # 1st house (same sign as Moon)
            sade_sati = {"active": True, "phase": "peak"}
        elif saturn_from_moon == 1:  # 2nd house
            sade_sati = {"active": True, "phase": "setting"}

    # Ashtama Shani detection (Saturn in 8th from Moon)
    ashtama_shani = False
    if saturn_rashi is not None:
        saturn_house = (saturn_rashi - moon_rashi_index) % 12 + 1
        ashtama_shani = saturn_house == 8

    return {
        "transits": transits,
        "sade_sati": sade_sati,
        "ashtama_shani": ashtama_shani,
    }
