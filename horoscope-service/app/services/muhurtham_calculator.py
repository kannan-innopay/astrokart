"""
Muhurtham (auspicious timing) calculator.

Scans a date range for auspicious dates based on panchanga elements
(tithi, nakshatra, yoga, vaara) and purpose-specific rules.
When a user's birth chart is provided, adds personalized scoring based on
transit-over-natal houses, dasha period alignment, and lagna compatibility.
"""

from datetime import datetime, timedelta

from app.services.astro_constants import (
    DASHA_LORDS,
    DASHA_YEARS,
    SIGN_LORDS,
)
from app.services.nakshatra import NAKSHATRAS, get_nakshatra, get_rashi
from app.services.transit_calculator import get_current_positions

NAKSHATRA_SPAN = 13 + 1 / 3


def _positions_by_name(date: datetime) -> dict:
    """Get transit positions as a dict keyed by planet name."""
    data = get_current_positions(date)
    return {g["name"]: g for g in data.get("grahas", [])}


# Weekday names (0=Monday in Python's weekday())
WEEKDAYS = ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"]

TITHIS = [
    "Pratipada", "Dwitiya", "Tritiya", "Chaturthi", "Panchami",
    "Shashthi", "Saptami", "Ashtami", "Navami", "Dashami",
    "Ekadashi", "Dwadashi", "Trayodashi", "Chaturdashi", "Purnima",
    "Pratipada", "Dwitiya", "Tritiya", "Chaturthi", "Panchami",
    "Shashthi", "Saptami", "Ashtami", "Navami", "Dashami",
    "Ekadashi", "Dwadashi", "Trayodashi", "Chaturdashi", "Amavasya",
]

YOGAS = [
    "Vishkambha", "Priti", "Ayushman", "Saubhagya", "Shobhana",
    "Atiganda", "Sukarma", "Dhriti", "Shoola", "Ganda",
    "Vriddhi", "Dhruva", "Vyaghata", "Harshana", "Vajra",
    "Siddhi", "Vyatipata", "Variyan", "Parigha", "Shiva",
    "Siddha", "Sadhya", "Shubha", "Shukla", "Brahma",
    "Indra", "Vaidhriti",
]

MALEFIC_YOGAS = {0, 5, 8, 9, 12, 14, 16, 18, 26}

# Purpose-to-relevant-house mapping for transit scoring
PURPOSE_HOUSES = {
    "marriage": {"benefic": {7, 5, 11}, "karaka": "Venus"},
    "engagement": {"benefic": {7, 5, 11}, "karaka": "Venus"},
    "business_opening": {"benefic": {10, 2, 11}, "karaka": "Mercury"},
    "shop_opening": {"benefic": {10, 2, 11}, "karaka": "Mercury"},
    "housewarming": {"benefic": {4, 2, 11}, "karaka": "Moon"},
    "vehicle_purchase": {"benefic": {4, 11}, "karaka": "Venus"},
    "travel": {"benefic": {3, 9, 11}, "karaka": "Mercury"},
    "naming_ceremony": {"benefic": {5, 1, 11}, "karaka": "Jupiter"},
    "agreement_signing": {"benefic": {7, 10, 11}, "karaka": "Mercury"},
    "job_joining": {"benefic": {10, 6, 11}, "karaka": "Saturn"},
    "education": {"benefic": {5, 4, 9}, "karaka": "Jupiter"},
}

# Purpose-specific panchanga rules
MUHURTHAM_RULES = {
    "marriage": {
        "good_tithis": [2, 3, 5, 7, 10, 11, 13],
        "good_nakshatras": [3, 4, 6, 7, 10, 11, 12, 13, 20, 21, 26],
        "good_weekdays": [0, 2, 3, 4],
        "avoid_weekdays": [1, 5],
    },
    "engagement": {
        "good_tithis": [2, 3, 5, 7, 10, 11, 13],
        "good_nakshatras": [3, 4, 6, 7, 10, 11, 12, 13, 21, 26],
        "good_weekdays": [0, 2, 3, 4],
        "avoid_weekdays": [1, 5],
    },
    "business_opening": {
        "good_tithis": [2, 3, 5, 7, 10],
        "good_nakshatras": [2, 6, 7, 12, 13, 14, 20, 21, 26],
        "good_weekdays": [0, 2, 3, 4],
        "avoid_weekdays": [1, 5],
    },
    "shop_opening": {
        "good_tithis": [2, 3, 5, 7, 10],
        "good_nakshatras": [2, 6, 7, 12, 13, 14, 20, 21, 26],
        "good_weekdays": [0, 2, 3, 4],
        "avoid_weekdays": [1, 5],
    },
    "housewarming": {
        "good_tithis": [2, 3, 5, 7, 10, 11, 13],
        "good_nakshatras": [3, 6, 7, 11, 12, 13, 20, 21, 26],
        "good_weekdays": [0, 2, 3, 4],
        "avoid_weekdays": [1, 5],
    },
    "vehicle_purchase": {
        "good_tithis": [2, 3, 5, 7, 10, 11],
        "good_nakshatras": [0, 3, 6, 7, 12, 13, 14, 21, 26],
        "good_weekdays": [0, 2, 3, 4],
        "avoid_weekdays": [1, 5],
    },
    "travel": {
        "good_tithis": [2, 3, 5, 7, 10, 11],
        "good_nakshatras": [0, 4, 6, 7, 12, 14, 21, 26],
        "good_weekdays": [0, 2, 4],
        "avoid_weekdays": [1, 5],
    },
    "naming_ceremony": {
        "good_tithis": [2, 3, 5, 7, 10, 11],
        "good_nakshatras": [0, 3, 4, 6, 7, 10, 11, 12, 21, 26],
        "good_weekdays": [0, 2, 3, 4],
        "avoid_weekdays": [1, 5],
    },
    "agreement_signing": {
        "good_tithis": [2, 3, 5, 7, 10, 11, 13],
        "good_nakshatras": [2, 6, 7, 12, 13, 14, 20, 21, 26],
        "good_weekdays": [0, 2, 3, 4],
        "avoid_weekdays": [1, 5],
    },
    "job_joining": {
        "good_tithis": [2, 3, 5, 7, 10],
        "good_nakshatras": [0, 6, 7, 12, 13, 14, 20, 21, 26],
        "good_weekdays": [0, 2, 3, 4],
        "avoid_weekdays": [1, 5],
    },
    "education": {
        "good_tithis": [2, 3, 5, 7, 10, 11],
        "good_nakshatras": [0, 4, 6, 7, 12, 14, 21, 26],
        "good_weekdays": [0, 2, 3, 4],
        "avoid_weekdays": [1, 5],
    },
}

DEFAULT_RULES = {
    "good_tithis": [2, 3, 5, 7, 10, 11, 13],
    "good_nakshatras": [3, 6, 7, 12, 13, 14, 20, 21, 26],
    "good_weekdays": [0, 2, 3, 4],
    "avoid_weekdays": [1, 5],
}

# Nakshatra-to-dasha-lord mapping (same as analysis_engine)
NAKSHATRA_DASHA_LORD = {
    0: "Ketu", 1: "Venus", 2: "Sun", 3: "Moon", 4: "Mars", 5: "Rahu",
    6: "Jupiter", 7: "Saturn", 8: "Mercury", 9: "Ketu", 10: "Venus",
    11: "Sun", 12: "Moon", 13: "Mars", 14: "Rahu", 15: "Jupiter",
    16: "Saturn", 17: "Mercury", 18: "Ketu", 19: "Venus", 20: "Sun",
    21: "Moon", 22: "Mars", 23: "Rahu", 24: "Jupiter", 25: "Saturn",
    26: "Mercury",
}


def find_auspicious_dates(
    date_start: str,
    date_end: str,
    purpose: str,
    user_chart: dict | None = None,
) -> list[dict]:
    """
    Scan a date range for auspicious muhurtham dates.

    If user_chart is provided (birth chart data), personalized scoring is added
    based on transit positions over natal houses, dasha alignment, and lagna.
    """
    start = datetime.strptime(date_start, "%Y-%m-%d")
    end = datetime.strptime(date_end, "%Y-%m-%d")

    if (end - start).days > 90:
        end = start + timedelta(days=90)

    rules = MUHURTHAM_RULES.get(purpose, DEFAULT_RULES)

    # Extract user's natal data if chart provided
    natal = _extract_natal_data(user_chart) if user_chart else None

    results = []
    current = start
    while current <= end:
        score, reasons = _score_date(current, rules, purpose, natal)

        if score >= 40:
            positions = _positions_by_name(current)
            moon_data = positions.get("Moon", {})
            moon_longitude = moon_data.get("longitude", 0)
            sun_data = positions.get("Sun", {})
            sun_long = sun_data.get("longitude", 0)

            nakshatra_info = get_nakshatra(moon_longitude)
            rashi_info = get_rashi(moon_longitude)
            tithi_index = int(((moon_longitude - sun_long) % 360) / 12)
            tithi_name = TITHIS[tithi_index] if 0 <= tithi_index < 30 else "Unknown"
            yoga_index = int((sun_long + moon_longitude) % 360 / (360 / 27))
            yoga_name = YOGAS[yoga_index] if 0 <= yoga_index < 27 else "Unknown"

            result = {
                "date": current.strftime("%Y-%m-%d"),
                "weekday": WEEKDAYS[current.weekday()],
                "score": score,
                "tithi": tithi_name,
                "nakshatra": nakshatra_info["name"],
                "nakshatra_index": nakshatra_info["index"],
                "rashi": rashi_info["name"],
                "rashi_index": rashi_info["index"],
                "yoga": yoga_name,
                "reasons": reasons,
                "personalized": natal is not None,
            }

            # Add transit details for personalized results
            if natal:
                result["transit_highlights"] = _get_transit_highlights(
                    positions, natal, purpose
                )

            results.append(result)

        current += timedelta(days=1)

    results.sort(key=lambda x: x["score"], reverse=True)
    return results[:10]


def _extract_natal_data(chart: dict) -> dict | None:
    """Extract key natal data from birth chart for personalized scoring."""
    if not chart:
        return None

    lagna = chart.get("lagna", {})
    grahas = chart.get("grahas", [])

    lagna_index = lagna.get("rashi", {}).get("index")
    if lagna_index is None:
        return None

    # Find Moon
    moon_nak_index = None
    moon_rashi_index = None
    for g in grahas:
        if g["name"] == "Moon":
            moon_nak_index = g.get("nakshatra", {}).get("index")
            moon_rashi_index = g.get("rashi", {}).get("index")
            break

    if moon_nak_index is None or moon_rashi_index is None:
        return None

    # Get current dasha lord from Moon's nakshatra
    dasha_lord = NAKSHATRA_DASHA_LORD.get(moon_nak_index, "Unknown")

    # Build natal planet positions by house
    natal_planets = {}
    for g in grahas:
        natal_planets[g["name"]] = {
            "house": g.get("house", 0),
            "rashi_index": g.get("rashi", {}).get("index", 0),
        }

    return {
        "lagna_index": lagna_index,
        "moon_rashi_index": moon_rashi_index,
        "moon_nak_index": moon_nak_index,
        "dasha_lord": dasha_lord,
        "natal_planets": natal_planets,
    }


def _score_date(
    date: datetime,
    rules: dict,
    purpose: str,
    natal: dict | None = None,
) -> tuple[int, list[str]]:
    """Score a single date. Returns (score, reasons)."""
    score = 0
    reasons = []

    positions = _positions_by_name(date)
    moon_data = positions.get("Moon", {})
    sun_data = positions.get("Sun", {})

    moon_longitude = moon_data.get("longitude", 0)
    sun_longitude = sun_data.get("longitude", 0)

    # --- UNIVERSAL PANCHANGA SCORING (max ~70) ---

    # 1. Tithi (+20)
    tithi_index = int(((moon_longitude - sun_longitude) % 360) / 12)
    tithi_number = (tithi_index % 15) + 1

    if tithi_number in rules.get("good_tithis", []):
        score += 20
        reasons.append("Auspicious tithi")
    elif tithi_number in {4, 8, 9, 14}:
        score -= 10
        reasons.append("Rikta tithi (less favorable)")

    # 2. Nakshatra (+20)
    nakshatra_info = get_nakshatra(moon_longitude)
    nak_index = nakshatra_info["index"]

    if nak_index in rules.get("good_nakshatras", []):
        score += 20
        reasons.append(f"Auspicious nakshatra ({nakshatra_info['name']})")

    # 3. Weekday (+15)
    weekday = date.weekday()
    if weekday in rules.get("good_weekdays", []):
        score += 15
        reasons.append(f"Favorable weekday ({WEEKDAYS[weekday]})")
    elif weekday in rules.get("avoid_weekdays", []):
        score -= 15
        reasons.append(f"Unfavorable weekday ({WEEKDAYS[weekday]})")

    # 4. Yoga (+15)
    yoga_index = int((sun_longitude + moon_longitude) % 360 / (360 / 27))
    if yoga_index not in MALEFIC_YOGAS:
        score += 15
        reasons.append("No malefic yoga")
    else:
        score -= 10
        reasons.append(f"Malefic yoga ({YOGAS[yoga_index]})")

    # 5. Amavasya/Purnima
    if tithi_index == 14:
        score += 5
        reasons.append("Purnima (full moon)")
    elif tithi_index == 29:
        score -= 15
        reasons.append("Amavasya (new moon — generally avoided)")

    # --- PERSONALIZED SCORING (max ~30 extra, only if natal data provided) ---

    if natal:
        personal_score, personal_reasons = _personal_score(
            positions, natal, purpose, nak_index
        )
        score += personal_score
        reasons.extend(personal_reasons)

    score = max(0, min(100, score))
    return score, reasons


def _personal_score(
    positions: dict,
    natal: dict,
    purpose: str,
    transit_moon_nak_index: int,
) -> tuple[int, list[str]]:
    """Score personalization based on user's birth chart."""
    score = 0
    reasons = []

    lagna_index = natal["lagna_index"]
    moon_rashi = natal["moon_rashi_index"]
    moon_nak = natal["moon_nak_index"]
    dasha_lord = natal["dasha_lord"]
    purpose_config = PURPOSE_HOUSES.get(purpose, {"benefic": {10, 11}, "karaka": "Jupiter"})

    # 1. Tara (star compatibility) from natal Moon (+10 / -5)
    tara = (transit_moon_nak_index - moon_nak) % 27
    tara_group = (tara % 9) + 1
    tara_names = {1: "Janma", 2: "Sampat", 3: "Vipat", 4: "Kshema",
                  5: "Pratyari", 6: "Sadhana", 7: "Naidhana", 8: "Mitra", 9: "Ati-Mitra"}

    if tara_group in {2, 4, 6, 8, 9}:
        score += 10
        reasons.append(f"Favorable tara: {tara_names.get(tara_group, '')} (for you)")
    elif tara_group in {3, 5, 7}:
        score -= 5
        reasons.append(f"Unfavorable tara: {tara_names.get(tara_group, '')} (for you)")

    # 2. Transit Jupiter in benefic house from lagna (+8)
    jupiter = positions.get("Jupiter", {})
    if jupiter:
        j_house = ((jupiter.get("rashi_index", 0) - lagna_index) % 12) + 1
        if j_house in purpose_config["benefic"] or j_house in {1, 5, 9}:
            score += 8
            reasons.append(f"Jupiter transiting your {_ordinal(j_house)} house (favorable)")

    # 3. Purpose karaka planet in good position (+6)
    karaka_name = purpose_config["karaka"]
    karaka = positions.get(karaka_name, {})
    if karaka:
        k_house = ((karaka.get("rashi_index", 0) - lagna_index) % 12) + 1
        if k_house in purpose_config["benefic"] or k_house in {1, 5, 9, 11}:
            score += 6
            reasons.append(f"{karaka_name} well-placed in your {_ordinal(k_house)} house")
        elif k_house in {6, 8, 12}:
            score -= 3
            reasons.append(f"{karaka_name} in your {_ordinal(k_house)} house (challenging)")

    # 4. Transit Moon house from lagna (+4 / -3)
    transit_moon = positions.get("Moon", {})
    if transit_moon:
        m_house = ((transit_moon.get("rashi_index", 0) - lagna_index) % 12) + 1
        if m_house in {1, 2, 5, 9, 11}:
            score += 4
            reasons.append(f"Transit Moon in your {_ordinal(m_house)} house (supportive)")
        elif m_house in {6, 8, 12}:
            score -= 3
            reasons.append(f"Transit Moon in your {_ordinal(m_house)} house (less supportive)")

    # 5. Dasha lord alignment (+5)
    # If dasha lord is the karaka or rules a benefic house for this purpose
    if dasha_lord == karaka_name:
        score += 5
        reasons.append(f"Your Dasha lord ({dasha_lord}) aligns with this purpose")
    elif dasha_lord in {"Jupiter", "Venus"} and purpose in {"marriage", "engagement", "housewarming"}:
        score += 3
        reasons.append(f"Benefic Dasha lord ({dasha_lord}) supports this event")

    # 6. No malefic transit over natal lagna (-3)
    for malefic in ["Saturn", "Mars", "Rahu"]:
        m = positions.get(malefic, {})
        if m and ((m.get("rashi_index", 0) - lagna_index) % 12) == 0:
            score -= 3
            reasons.append(f"{malefic} transiting your lagna (caution)")
            break

    return score, reasons


def _get_transit_highlights(
    positions: dict,
    natal: dict,
    purpose: str,
) -> list[dict]:
    """Get transit highlights for display on the results page."""
    highlights = []
    lagna_index = natal["lagna_index"]
    purpose_config = PURPOSE_HOUSES.get(purpose, {"benefic": {10, 11}, "karaka": "Jupiter"})

    for planet_name in ["Jupiter", "Saturn", "Venus", "Mars", "Mercury"]:
        p = positions.get(planet_name, {})
        if not p:
            continue

        house = ((p.get("rashi_index", 0) - lagna_index) % 12) + 1
        rashi = p.get("rashi_name", "")

        is_favorable = house in purpose_config["benefic"] or house in {1, 5, 9, 11}
        is_challenging = house in {6, 8, 12}

        highlights.append({
            "planet": planet_name,
            "rashi": rashi,
            "rashi_index": int(p.get("rashi_index", 0)),
            "house_from_lagna": house,
            "is_retrograde": bool(p.get("is_retrograde", False)),
            "effect": "favorable" if is_favorable else ("challenging" if is_challenging else "neutral"),
        })

    return highlights


def _ordinal(n: int) -> str:
    suffixes = {1: "1st", 2: "2nd", 3: "3rd"}
    return suffixes.get(n, f"{n}th")
