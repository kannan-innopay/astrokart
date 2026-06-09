"""
Daily and monthly prediction engine.

Combines transit positions, Dasha periods, and house positions
to generate personalized predictions. All text is rule-based.
"""

from datetime import datetime, timedelta

from app.services.analysis_engine import (
    calculate_vimshottari_dasha,
    get_planet_by_name,
)
from app.services.astro_constants import (
    DASHA_INTERPRETATIONS,
    ENGINE_VERSION,
    KENDRA_HOUSES,
    RASHI_ELEMENTS,
    SIGN_LORDS,
    TRIKONA_HOUSES,
    TRIK_HOUSES,
)
from app.services.transit_calculator import get_current_positions


def _positions_by_name(date) -> dict:
    """Get transit positions as a dict keyed by planet name."""
    data = get_current_positions(date)
    return {g["name"]: g for g in data.get("grahas", [])}

# Lucky colors per planet
LUCKY_COLORS = {
    "Sun": "Orange", "Moon": "White", "Mars": "Red",
    "Mercury": "Green", "Jupiter": "Yellow", "Venus": "Pink",
    "Saturn": "Blue", "Rahu": "Grey", "Ketu": "Brown",
}

# Lucky numbers per planet
LUCKY_NUMBERS = {
    "Sun": 1, "Moon": 2, "Mars": 9, "Mercury": 5,
    "Jupiter": 3, "Venus": 6, "Saturn": 8, "Rahu": 4, "Ketu": 7,
}

# Prediction text banks per life area, keyed by effect quality
CAREER_PREDICTIONS = {
    "benefic": [
        "Career matters may see positive momentum today. Good time for important meetings and decisions.",
        "Professional recognition and advancement themes may be active. Your efforts could gain visibility.",
        "Favorable energy for taking initiative at work. New opportunities may present themselves.",
    ],
    "malefic": [
        "Workplace challenges may require extra patience today. Avoid impulsive career decisions.",
        "Professional setbacks could be temporary learning opportunities. Stay focused on long-term goals.",
        "Diplomacy at work is advisable today. Avoid conflicts with authority figures.",
    ],
    "mixed": [
        "A balanced day for career matters. Progress is possible with careful planning.",
        "Work routines may bring steady results today. Focus on completing pending tasks.",
        "Career-related communications may need extra attention. Double-check important details.",
    ],
}

RELATIONSHIP_PREDICTIONS = {
    "benefic": [
        "Relationships may bring joy and warmth today. Good time for expressing feelings.",
        "Social connections could flourish. You may attract positive attention from others.",
        "Harmony in partnerships is supported. A good day for important relationship conversations.",
    ],
    "malefic": [
        "Relationship dynamics may need extra care today. Patience and understanding are advised.",
        "Misunderstandings with close ones are possible. Choose words carefully.",
        "Personal relationships may feel challenging. Give yourself and others space.",
    ],
    "mixed": [
        "Relationships flow steadily today. Small gestures of kindness can have a big impact.",
        "Social interactions are neutral. A good day for maintaining existing connections.",
        "Family matters may need attention. Balance personal needs with responsibilities.",
    ],
}

FINANCE_PREDICTIONS = {
    "benefic": [
        "Financial matters may see positive developments. A favorable time for investments.",
        "Income opportunities could present themselves. Trust your financial instincts today.",
        "Material gains are supported. Good time for financial planning and savings.",
    ],
    "malefic": [
        "Financial caution is advisable today. Avoid impulsive purchases or risky investments.",
        "Unexpected expenses are possible. Focus on budget management and savings.",
        "Financial transactions may need extra scrutiny. Postpone major financial decisions if possible.",
    ],
    "mixed": [
        "Steady financial day. Routine transactions should proceed smoothly.",
        "Financial matters are neutral. A good time for reviewing budgets and plans.",
        "Moderate financial activity expected. Avoid extremes in spending or saving.",
    ],
}

HEALTH_PREDICTIONS = {
    "benefic": [
        "Physical vitality may feel strong today. A great day for exercise and outdoor activities.",
        "Mental and emotional wellness are supported. Meditation and rest can be deeply beneficial.",
        "Energy levels may be high. Channel this into productive physical activity.",
    ],
    "malefic": [
        "Energy levels may fluctuate. Prioritize rest and avoid overexertion.",
        "Minor health sensitivities are possible. Listen to your body's signals.",
        "Stress management is important today. Practice breathing exercises or light yoga.",
    ],
    "mixed": [
        "Moderate energy day. Maintain regular health routines for best results.",
        "Physical wellness is stable. Stay hydrated and get adequate sleep.",
        "Balance activity and rest today. Gentle exercise is recommended.",
    ],
}

OVERALL_ADVICE = {
    5: "An excellent day ahead. Cosmic energies strongly support your endeavors. Make the most of this positive alignment.",
    4: "A good day with favorable celestial influences. Move forward with confidence on important matters.",
    3: "A balanced day. Steady progress is possible with mindful effort. Stay grounded.",
    2: "A day requiring patience and caution. Focus on routine tasks and avoid major new initiatives.",
    1: "A challenging day. Rest, reflect, and recharge. Better cosmic alignments are on the way.",
}


def generate_daily_prediction(
    chart_data: dict,
    date_of_birth: str,
    target_date: str,
) -> dict:
    """Generate a personalized daily prediction."""
    target = datetime.strptime(target_date, "%Y-%m-%d")

    # Get transit positions for the target date
    positions = _positions_by_name(target)

    # Get user's lagna and moon
    lagna_index = chart_data["lagna"]["rashi"]["index"]
    moon_graha = get_planet_by_name(chart_data["grahas"], "Moon")
    moon_rashi_index = moon_graha["rashi"]["index"] if moon_graha else 0

    # Get current dasha period
    dasha_data = calculate_vimshottari_dasha(chart_data, date_of_birth)
    current_md_lord = dasha_data.get("current_mahadasha", {}).get("lord", "Unknown") if dasha_data.get("current_mahadasha") else "Unknown"
    current_ad_lord = dasha_data.get("current_antardasha", {}).get("lord") if dasha_data.get("current_antardasha") else None

    # Score each life area based on transiting planets
    career_score = _score_area(positions, lagna_index, moon_rashi_index, area="career")
    relationship_score = _score_area(positions, lagna_index, moon_rashi_index, area="relationships")
    finance_score = _score_area(positions, lagna_index, moon_rashi_index, area="finance")
    health_score = _score_area(positions, lagna_index, moon_rashi_index, area="health")

    overall_score = round((career_score + relationship_score + finance_score + health_score) / 4)
    overall_score = max(1, min(5, overall_score))

    # Determine lucky color from dasha lord
    lucky_color = LUCKY_COLORS.get(current_md_lord, "White")
    lucky_number = LUCKY_NUMBERS.get(current_md_lord, 1)
    if current_ad_lord:
        lucky_number = (lucky_number + LUCKY_NUMBERS.get(current_ad_lord, 0)) % 9 + 1

    # Select prediction texts
    career_text = _select_prediction(CAREER_PREDICTIONS, career_score)
    relationship_text = _select_prediction(RELATIONSHIP_PREDICTIONS, relationship_score)
    finance_text = _select_prediction(FINANCE_PREDICTIONS, finance_score)
    health_text = _select_prediction(HEALTH_PREDICTIONS, health_score)

    return {
        "date": target_date,
        "overall_score": overall_score,
        "sections": {
            "career": {"score": career_score, "prediction": career_text},
            "relationships": {"score": relationship_score, "prediction": relationship_text},
            "finance": {"score": finance_score, "prediction": finance_text},
            "health": {"score": health_score, "prediction": health_text},
        },
        "lucky": {
            "color": lucky_color,
            "number": lucky_number,
        },
        "dasha": {
            "mahadasha": current_md_lord,
            "antardasha": current_ad_lord,
        },
        "advice": OVERALL_ADVICE.get(overall_score, OVERALL_ADVICE[3]),
        "engine_version": ENGINE_VERSION,
    }


def generate_monthly_forecast(
    chart_data: dict,
    date_of_birth: str,
    year: int,
    month: int,
) -> dict:
    """Generate a monthly forecast with week-by-week breakdown."""
    from calendar import monthrange

    _, days_in_month = monthrange(year, month)
    month_start = datetime(year, month, 1)

    lagna_index = chart_data["lagna"]["rashi"]["index"]
    moon_graha = get_planet_by_name(chart_data["grahas"], "Moon")
    moon_rashi_index = moon_graha["rashi"]["index"] if moon_graha else 0

    # Get dasha info
    dasha_data = calculate_vimshottari_dasha(chart_data, date_of_birth)
    current_md = dasha_data.get("current_mahadasha", {})

    # Generate weekly summaries
    weeks = []
    week_start = month_start

    week_num = 1
    while week_start.month == month:
        week_end = min(week_start + timedelta(days=6), datetime(year, month, days_in_month))
        if week_end.month != month:
            week_end = datetime(year, month, days_in_month)

        # Sample mid-week for transit assessment
        mid_week = week_start + timedelta(days=3)
        if mid_week.month != month:
            mid_week = week_start

        positions = _positions_by_name(mid_week)

        career_s = _score_area(positions, lagna_index, moon_rashi_index, "career")
        rel_s = _score_area(positions, lagna_index, moon_rashi_index, "relationships")
        fin_s = _score_area(positions, lagna_index, moon_rashi_index, "finance")
        health_s = _score_area(positions, lagna_index, moon_rashi_index, "health")
        avg_score = round((career_s + rel_s + fin_s + health_s) / 4)

        # Note key transits this week
        key_transits = []
        for planet_name in ["Jupiter", "Saturn", "Venus", "Mars"]:
            p_data = positions.get(planet_name, {})
            if p_data:
                house_from_moon = ((p_data.get("rashi_index", 0) - moon_rashi_index) % 12) + 1
                key_transits.append({
                    "planet": planet_name,
                    "rashi": p_data.get("rashi_name", ""),
                    "house_from_moon": int(house_from_moon),
                    "is_retrograde": bool(p_data.get("is_retrograde", False)),
                })

        themes = []
        if career_s >= 4:
            themes.append("Career growth supported")
        elif career_s <= 2:
            themes.append("Career patience needed")
        if rel_s >= 4:
            themes.append("Harmonious relationships")
        if fin_s >= 4:
            themes.append("Financial opportunities")
        elif fin_s <= 2:
            themes.append("Financial caution advised")
        if health_s >= 4:
            themes.append("Strong vitality")

        weeks.append({
            "week_number": week_num,
            "start_date": week_start.strftime("%Y-%m-%d"),
            "end_date": week_end.strftime("%Y-%m-%d"),
            "overall_score": max(1, min(5, avg_score)),
            "themes": themes if themes else ["Steady progress"],
            "key_transits": key_transits,
        })

        week_start = week_end + timedelta(days=1)
        week_num += 1

    # Monthly overview
    avg_monthly = round(sum(w["overall_score"] for w in weeks) / len(weeks)) if weeks else 3

    return {
        "month": month,
        "year": year,
        "overall_score": max(1, min(5, avg_monthly)),
        "overview": OVERALL_ADVICE.get(avg_monthly, OVERALL_ADVICE[3]),
        "dasha": {
            "mahadasha": current_md.get("lord", "Unknown"),
            "interpretation": DASHA_INTERPRETATIONS.get(current_md.get("lord", ""), ""),
        },
        "weeks": weeks,
        "engine_version": ENGINE_VERSION,
    }


def _score_area(
    positions: dict,
    lagna_index: int,
    moon_rashi_index: int,
    area: str,
) -> int:
    """Score a life area (1-5) based on transiting planets."""
    # Relevant houses per area
    area_houses = {
        "career": {2, 6, 10, 11},
        "relationships": {5, 7, 11},
        "finance": {2, 5, 9, 11},
        "health": {1, 6, 8},
    }

    relevant = area_houses.get(area, {10})
    score = 3  # baseline neutral

    for planet_name in ["Jupiter", "Saturn", "Mars", "Venus", "Mercury", "Sun", "Moon"]:
        p_data = positions.get(planet_name, {})
        if not p_data:
            continue

        p_rashi = p_data.get("rashi_index", 0)
        house_from_lagna = ((p_rashi - lagna_index) % 12) + 1
        house_from_moon = ((p_rashi - moon_rashi_index) % 12) + 1

        if house_from_lagna in relevant or house_from_moon in relevant:
            # Benefic planets in relevant houses boost score
            if planet_name in {"Jupiter", "Venus"}:
                score += 0.5
            elif planet_name in {"Saturn", "Mars", "Rahu", "Ketu"}:
                # Malefics in trik houses (6,8,12) can be good (viparita raja yoga concept)
                if house_from_lagna in TRIK_HOUSES:
                    score += 0.3
                else:
                    score -= 0.3

    # Bonus for Jupiter in kendra from lagna
    jupiter = positions.get("Jupiter", {})
    if jupiter:
        j_house = ((jupiter.get("rashi_index", 0) - lagna_index) % 12) + 1
        if j_house in KENDRA_HOUSES:
            score += 0.5

    return max(1, min(5, round(score)))


def _select_prediction(bank: dict, score: int) -> str:
    """Select prediction text based on score."""
    import hashlib
    from datetime import datetime as dt

    if score >= 4:
        texts = bank["benefic"]
    elif score <= 2:
        texts = bank["malefic"]
    else:
        texts = bank["mixed"]

    # Use date-based hash for consistent but varying selection
    day_hash = int(hashlib.md5(dt.now().strftime("%Y-%m-%d").encode()).hexdigest(), 16)
    return texts[day_hash % len(texts)]
