from datetime import datetime

from jyotishganit import calculate_birth_chart

from app.models.request import ChartRequest
from app.models.response import (
    ChartResponse,
    GrahaInfo,
    HouseInfo,
    LagnaInfo,
    NakshatraInfo,
    PanchangaInfo,
    RashiInfo,
)
from app.services.nakshatra import RASHIS, get_graha_sanskrit

NAKSHATRA_SPAN = 13 + 1 / 3  # 13°20'


def generate_chart(request: ChartRequest) -> ChartResponse:
    """Generate a Vedic birth chart using jyotishganit."""
    date_parts = request.date_of_birth.split("-")
    time_parts = request.time_of_birth.split(":")

    birth_dt = datetime(
        int(date_parts[0]),
        int(date_parts[1]),
        int(date_parts[2]),
        int(time_parts[0]),
        int(time_parts[1]),
        0,
    )

    chart = calculate_birth_chart(
        birth_date=birth_dt,
        latitude=request.latitude,
        longitude=request.longitude,
        timezone_offset=request.timezone_offset,
        name=request.name,
        location_name=request.place_of_birth,
    )

    ayanamsa_value = float(chart.ayanamsa.value)

    # Lagna (ascendant) from first house
    lagna_house = chart.d1_chart.houses[0]
    lagna_rashi = _sign_to_rashi(lagna_house.sign)
    lagna_deg = float(lagna_house.sign_degrees) if lagna_house.sign_degrees is not None else 0.0
    lagna_abs = lagna_rashi["index"] * 30.0 + lagna_deg

    lagna = LagnaInfo(
        longitude=round(lagna_abs, 4),
        rashi=RashiInfo(**lagna_rashi),
        nakshatra=NakshatraInfo(
            index=int(lagna_abs / NAKSHATRA_SPAN) % 27,
            name=lagna_house.nakshatra or "",
            pada=lagna_house.pada or 1,
        ),
    )

    # Grahas
    grahas = []
    for planet in chart.d1_chart.planets:
        rashi = _sign_to_rashi(planet.sign)
        sign_deg = float(planet.sign_degrees) if planet.sign_degrees is not None else 0.0
        abs_longitude = rashi["index"] * 30.0 + sign_deg

        grahas.append(
            GrahaInfo(
                name=planet.celestial_body,
                sanskrit=get_graha_sanskrit(planet.celestial_body),
                longitude=round(abs_longitude, 4),
                is_retrograde=planet.motion_type == "retrograde",
                rashi=RashiInfo(**rashi),
                nakshatra=NakshatraInfo(
                    index=int(abs_longitude / NAKSHATRA_SPAN) % 27,
                    name=planet.nakshatra,
                    pada=planet.pada,
                ),
                house=planet.house,
            )
        )

    # Houses
    houses = []
    for house in chart.d1_chart.houses:
        rashi = _sign_to_rashi(house.sign)
        houses.append(
            HouseInfo(
                number=house.number,
                sign_index=rashi["index"],
                sign=rashi["name"],
                sign_sanskrit=rashi["sanskrit"],
            )
        )

    # Panchanga
    panchanga = PanchangaInfo(
        tithi=str(chart.panchanga.tithi),
        nakshatra=str(chart.panchanga.nakshatra),
        yoga=str(chart.panchanga.yoga),
        karana=str(chart.panchanga.karana),
        vaara=str(chart.panchanga.vaara),
    )

    return ChartResponse(
        ayanamsa=round(ayanamsa_value, 4),
        lagna=lagna,
        grahas=grahas,
        houses=houses,
        panchanga=panchanga,
    )


def _sign_to_rashi(sign_name: str) -> dict:
    """Convert a sign name string to rashi dict."""
    sign_lower = sign_name.lower().strip()
    for i, r in enumerate(RASHIS):
        if r["name"].lower() == sign_lower or r["sanskrit"].lower() == sign_lower:
            return {"index": i, "name": r["name"], "sanskrit": r["sanskrit"]}
    return {"index": 0, "name": sign_name, "sanskrit": sign_name}
