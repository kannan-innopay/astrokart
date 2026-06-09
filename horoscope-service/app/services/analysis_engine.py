"""
Vedic astrology analysis engine.

Rule-based analysis of birth chart data. All functions take the chart_data dict
(same structure as stored in users.birth_chart) and return structured results.
"""

from datetime import datetime, timedelta

from app.services.astro_constants import (
    BODY_PARTS,
    CAREER_FIELDS,
    DASHA_INTERPRETATIONS,
    DASHA_LORDS,
    DASHA_TOTAL_YEARS,
    DASHA_YEARS,
    DEBILITATION,
    DECADE_PREDICTIONS,
    DHANA_YOGA_DESCRIPTIONS,
    DISCLAIMERS,
    ENGINE_VERSION,
    EXALTATION,
    KENDRA_HOUSES,
    MANGLIK_TEXT,
    NAKSHATRA_DASHA_LORD,
    NAKSHATRA_TRAITS,
    NATURAL_BENEFICS,
    OWN_SIGNS,
    PLANET_IN_HOUSE,
    RASHI_ELEMENTS,
    RASHI_MOON_NATURE,
    RASHI_PERSONALITY,
    RASHI_QUALITIES,
    SIGN_LORDS,
    TRANSIT_EFFECTS,
    TRIK_HOUSES,
    TRIKONA_HOUSES,
)

NAKSHATRA_SPAN = 13 + 1 / 3  # 13°20'


def get_house_lord(houses: list[dict], house_number: int) -> str:
    """Get the planet that rules a given house."""
    for house in houses:
        if house["number"] == house_number:
            return SIGN_LORDS.get(house["sign_index"], "Unknown")
    return "Unknown"


def get_planet_by_name(grahas: list[dict], name: str) -> dict | None:
    """Find a graha by name."""
    for g in grahas:
        if g["name"] == name:
            return g
    return None


def get_planet_strength(graha: dict, lagna_index: int) -> dict:
    """Compute a simple strength score (0-10) for a planet."""
    name = graha["name"]
    rashi_index = graha["rashi"]["index"]
    house = graha.get("house", 0)
    score = 5  # baseline

    reasons = []

    # Own sign
    if rashi_index in OWN_SIGNS.get(name, []):
        score += 2
        reasons.append("In own sign")

    # Exaltation / debilitation
    if EXALTATION.get(name) == rashi_index:
        score += 3
        reasons.append("Exalted")
    elif DEBILITATION.get(name) == rashi_index:
        score -= 3
        reasons.append("Debilitated")

    # House position
    if house in KENDRA_HOUSES:
        score += 1
        reasons.append("In kendra house")
    if house in TRIKONA_HOUSES:
        score += 2
        reasons.append("In trikona house")
    if house in TRIK_HOUSES:
        score -= 2
        reasons.append("In trik house")

    # Retrograde (outer planets gain, inner lose)
    if graha.get("is_retrograde"):
        if name in {"Jupiter", "Saturn", "Mars"}:
            score += 1
            reasons.append("Retrograde (strengthened)")
        elif name in {"Mercury", "Venus"}:
            score -= 1
            reasons.append("Retrograde (weakened)")

    score = max(0, min(10, score))

    return {"score": score, "reasons": reasons}


def analyze_personality(chart_data: dict) -> dict:
    """Analyze personality based on lagna, Moon, Sun, and 1st house planets."""
    lagna = chart_data["lagna"]
    grahas = chart_data["grahas"]

    lagna_index = lagna["rashi"]["index"]
    lagna_nakshatra_index = lagna["nakshatra"]["index"]

    # Moon and Sun signs
    moon = get_planet_by_name(grahas, "Moon")
    sun = get_planet_by_name(grahas, "Sun")

    moon_index = moon["rashi"]["index"] if moon else 0
    sun_index = sun["rashi"]["index"] if sun else 0

    # Planets in 1st house
    first_house_planets = [g for g in grahas if g.get("house") == 1]
    first_house_effects = []
    for g in first_house_planets:
        effect = PLANET_IN_HOUSE.get(g["name"], {}).get(1)
        if effect:
            first_house_effects.append({"planet": g["name"], "effect": effect})

    # Dominant element
    element_counts = {}
    for g in grahas:
        el = RASHI_ELEMENTS.get(g["rashi"]["index"], "Unknown")
        element_counts[el] = element_counts.get(el, 0) + 1
    # Add lagna element weight (double)
    lagna_el = RASHI_ELEMENTS.get(lagna_index, "Unknown")
    element_counts[lagna_el] = element_counts.get(lagna_el, 0) + 2
    dominant_element = max(element_counts, key=element_counts.get)

    return {
        "lagna_rashi": lagna["rashi"]["name"],
        "lagna_rashi_index": lagna_index,
        "lagna_traits": RASHI_PERSONALITY.get(lagna_index, ""),
        "moon_sign": moon["rashi"]["name"] if moon else "",
        "moon_nature": RASHI_MOON_NATURE.get(moon_index, ""),
        "sun_sign": sun["rashi"]["name"] if sun else "",
        "sun_identity": RASHI_PERSONALITY.get(sun_index, ""),
        "nakshatra_name": lagna["nakshatra"]["name"],
        "nakshatra_traits": NAKSHATRA_TRAITS.get(lagna_nakshatra_index, ""),
        "first_house_effects": first_house_effects,
        "dominant_element": dominant_element,
        "element_description": _element_description(dominant_element),
        "summary": f"With {lagna['rashi']['name']} as your ascendant and Moon in {moon['rashi']['name'] if moon else 'unknown'}, "
                   f"your chart suggests a blend of {RASHI_ELEMENTS.get(lagna_index, '')} and {RASHI_ELEMENTS.get(moon_index, '')} "
                   f"energies. Your dominant element is {dominant_element}.",
    }


def analyze_career(chart_data: dict) -> dict:
    """Analyze career indicators based on 10th house and related factors."""
    houses = chart_data["houses"]
    grahas = chart_data["grahas"]

    tenth_lord = get_house_lord(houses, 10)
    tenth_lord_planet = get_planet_by_name(grahas, tenth_lord)
    tenth_lord_house = tenth_lord_planet["house"] if tenth_lord_planet else None
    tenth_lord_strength = get_planet_strength(tenth_lord_planet, chart_data["lagna"]["rashi"]["index"]) if tenth_lord_planet else None

    # Planets in 10th house
    planets_in_10th = [g for g in grahas if g.get("house") == 10]
    tenth_house_effects = []
    for g in planets_in_10th:
        effect = PLANET_IN_HOUSE.get(g["name"], {}).get(10)
        if effect:
            tenth_house_effects.append({"planet": g["name"], "effect": effect})

    # Supporting house lords
    second_lord = get_house_lord(houses, 2)
    sixth_lord = get_house_lord(houses, 6)
    eleventh_lord = get_house_lord(houses, 11)

    # Determine strongest career planet
    career_planets = set()
    career_planets.add(tenth_lord)
    for g in planets_in_10th:
        career_planets.add(g["name"])
    career_planets.add(second_lord)

    # Recommend fields based on dominant career planets
    recommended_fields = []
    for planet in career_planets:
        fields = CAREER_FIELDS.get(planet, [])
        for f in fields:
            if f not in recommended_fields:
                recommended_fields.append(f)

    # Planet strengths for career
    strengths = {}
    for planet_name in ["Mercury", "Jupiter", "Mars", "Venus", "Saturn", "Sun"]:
        planet = get_planet_by_name(grahas, planet_name)
        if planet:
            strengths[planet_name] = get_planet_strength(planet, chart_data["lagna"]["rashi"]["index"])

    return {
        "tenth_house_lord": tenth_lord,
        "tenth_lord_house": tenth_lord_house,
        "tenth_lord_strength": tenth_lord_strength,
        "planets_in_10th": [g["name"] for g in planets_in_10th],
        "tenth_house_effects": tenth_house_effects,
        "second_lord": second_lord,
        "sixth_lord": sixth_lord,
        "eleventh_lord": eleventh_lord,
        "recommended_fields": recommended_fields[:8],
        "planet_strengths": strengths,
        "summary": f"Your 10th lord is {tenth_lord}"
                   + (f", placed in the {_ordinal(tenth_lord_house)} house" if tenth_lord_house else "")
                   + ". "
                   + (f"With {', '.join(g['name'] for g in planets_in_10th)} in the 10th house, " if planets_in_10th else "")
                   + "your chart suggests potential in fields related to "
                   + f"{', '.join(recommended_fields[:3])}.",
    }


def analyze_marriage(chart_data: dict) -> dict:
    """Analyze marriage/relationship indicators."""
    houses = chart_data["houses"]
    grahas = chart_data["grahas"]
    lagna_index = chart_data["lagna"]["rashi"]["index"]

    seventh_lord = get_house_lord(houses, 7)
    seventh_lord_planet = get_planet_by_name(grahas, seventh_lord)
    seventh_lord_house = seventh_lord_planet["house"] if seventh_lord_planet else None
    seventh_lord_strength = get_planet_strength(seventh_lord_planet, lagna_index) if seventh_lord_planet else None

    # Venus analysis
    venus = get_planet_by_name(grahas, "Venus")
    venus_analysis = None
    if venus:
        venus_analysis = {
            "house": venus["house"],
            "rashi": venus["rashi"]["name"],
            "strength": get_planet_strength(venus, lagna_index),
            "effect": PLANET_IN_HOUSE.get("Venus", {}).get(venus["house"], ""),
        }

    # Manglik check: Mars in houses 1, 2, 4, 7, 8, 12
    mars = get_planet_by_name(grahas, "Mars")
    manglik_houses = {1, 2, 4, 7, 8, 12}
    mars_house = mars["house"] if mars else 0

    if mars_house in manglik_houses:
        # Check for cancellation factors
        cancellation = False
        # Jupiter in 1, 4, 7, 8 can cancel
        jupiter = get_planet_by_name(grahas, "Jupiter")
        if jupiter and jupiter["house"] in {1, 4, 7, 8}:
            cancellation = True
        # Venus in same house as Mars
        if venus and venus["house"] == mars_house:
            cancellation = True

        if cancellation:
            manglik_status = "partial"
            manglik_text = MANGLIK_TEXT["partial"]
        else:
            manglik_status = "manglik"
            manglik_text = MANGLIK_TEXT["manglik"]
    else:
        manglik_status = "non_manglik"
        manglik_text = MANGLIK_TEXT["non_manglik"]

    # 5th house (romance)
    fifth_lord = get_house_lord(houses, 5)
    planets_in_5th = [g["name"] for g in grahas if g.get("house") == 5]

    # Planets in 7th
    planets_in_7th = [g for g in grahas if g.get("house") == 7]
    seventh_house_effects = []
    for g in planets_in_7th:
        effect = PLANET_IN_HOUSE.get(g["name"], {}).get(7)
        if effect:
            seventh_house_effects.append({"planet": g["name"], "effect": effect})

    return {
        "seventh_house_lord": seventh_lord,
        "seventh_lord_house": seventh_lord_house,
        "seventh_lord_strength": seventh_lord_strength,
        "seventh_house_effects": seventh_house_effects,
        "venus_analysis": venus_analysis,
        "manglik_status": manglik_status,
        "manglik_text": manglik_text,
        "fifth_lord": fifth_lord,
        "planets_in_5th": planets_in_5th,
        "disclaimer": DISCLAIMERS["marriage"],
        "summary": f"Your 7th lord is {seventh_lord}. "
                   + f"Manglik status: {manglik_status.replace('_', ' ').title()}. "
                   + (f"Venus is in the {_ordinal(venus['house'])} house in {venus['rashi']['name']}. " if venus else ""),
    }


def analyze_finance(chart_data: dict) -> dict:
    """Analyze financial indicators and Dhana yogas."""
    houses = chart_data["houses"]
    grahas = chart_data["grahas"]
    lagna_index = chart_data["lagna"]["rashi"]["index"]

    second_lord = get_house_lord(houses, 2)
    eleventh_lord = get_house_lord(houses, 11)

    second_lord_planet = get_planet_by_name(grahas, second_lord)
    eleventh_lord_planet = get_planet_by_name(grahas, eleventh_lord)

    second_lord_strength = get_planet_strength(second_lord_planet, lagna_index) if second_lord_planet else None
    eleventh_lord_strength = get_planet_strength(eleventh_lord_planet, lagna_index) if eleventh_lord_planet else None

    # Jupiter placement (natural wealth significator)
    jupiter = get_planet_by_name(grahas, "Jupiter")
    jupiter_analysis = None
    if jupiter:
        jupiter_analysis = {
            "house": jupiter["house"],
            "rashi": jupiter["rashi"]["name"],
            "strength": get_planet_strength(jupiter, lagna_index),
        }

    # Detect Dhana yogas
    dhana_yogas = []

    # 1. Lords of 2 and 11 in mutual kendras
    if second_lord_planet and eleventh_lord_planet:
        if second_lord_planet["house"] in KENDRA_HOUSES and eleventh_lord_planet["house"] in KENDRA_HOUSES:
            dhana_yogas.append(DHANA_YOGA_DESCRIPTIONS["lords_2_11_kendra"])

    # 2. Jupiter in wealth house
    if jupiter and jupiter["house"] in {2, 5, 9, 11}:
        dhana_yogas.append(DHANA_YOGA_DESCRIPTIONS["jupiter_wealth_house"])

    # 3. Venus exalted or own sign in kendra
    venus = get_planet_by_name(grahas, "Venus")
    if venus:
        venus_rashi = venus["rashi"]["index"]
        if (EXALTATION.get("Venus") == venus_rashi or venus_rashi in OWN_SIGNS.get("Venus", [])):
            if venus["house"] in KENDRA_HOUSES:
                dhana_yogas.append(DHANA_YOGA_DESCRIPTIONS["venus_exalted_kendra"])

    # 4. 9th lord in kendra
    ninth_lord = get_house_lord(houses, 9)
    ninth_lord_planet = get_planet_by_name(grahas, ninth_lord)
    if ninth_lord_planet and ninth_lord_planet["house"] in KENDRA_HOUSES:
        dhana_yogas.append(DHANA_YOGA_DESCRIPTIONS["lord_9_in_kendra"])

    # Wealth potential assessment
    total_score = 0
    if second_lord_strength:
        total_score += second_lord_strength["score"]
    if eleventh_lord_strength:
        total_score += eleventh_lord_strength["score"]
    if jupiter_analysis:
        total_score += jupiter_analysis["strength"]["score"]
    total_score += len(dhana_yogas) * 3

    if total_score >= 25:
        wealth_potential = "strong"
    elif total_score >= 15:
        wealth_potential = "moderate"
    else:
        wealth_potential = "developing"

    return {
        "second_lord": second_lord,
        "second_lord_house": second_lord_planet["house"] if second_lord_planet else None,
        "second_lord_strength": second_lord_strength,
        "eleventh_lord": eleventh_lord,
        "eleventh_lord_house": eleventh_lord_planet["house"] if eleventh_lord_planet else None,
        "eleventh_lord_strength": eleventh_lord_strength,
        "jupiter_analysis": jupiter_analysis,
        "dhana_yogas": dhana_yogas,
        "wealth_potential": wealth_potential,
        "disclaimer": DISCLAIMERS["finance"],
        "summary": f"Wealth potential: {wealth_potential.title()}. "
                   + f"Your 2nd lord is {second_lord} and 11th lord is {eleventh_lord}. "
                   + (f"{len(dhana_yogas)} Dhana yoga(s) detected. " if dhana_yogas else ""),
    }


def analyze_wellness(chart_data: dict) -> dict:
    """Analyze wellness tendencies (NOT medical predictions)."""
    houses = chart_data["houses"]
    grahas = chart_data["grahas"]
    lagna_index = chart_data["lagna"]["rashi"]["index"]

    # Constitution from lagna element
    constitution_map = {
        "Fire": "Pitta-dominant. You may have a warm constitution with strong digestive fire. Active and energetic tendencies.",
        "Earth": "Kapha-dominant. You may have a sturdy constitution with good endurance. Steady and grounded tendencies.",
        "Air": "Vata-dominant. You may have a light constitution with an active mind. Adaptable and quick-moving tendencies.",
        "Water": "Kapha-Pitta. You may have a sensitive constitution with strong intuitive awareness. Nurturing and receptive tendencies.",
    }
    lagna_element = RASHI_ELEMENTS.get(lagna_index, "Unknown")
    constitution = constitution_map.get(lagna_element, "Mixed constitution.")

    # 6th house analysis (health challenges area)
    sixth_lord = get_house_lord(houses, 6)
    planets_in_6th = [g["name"] for g in grahas if g.get("house") == 6]

    # 8th house analysis
    eighth_lord = get_house_lord(houses, 8)

    # Vulnerable body areas based on lagna and 6th house sign
    vulnerable_areas = []
    vulnerable_areas.append(BODY_PARTS.get(lagna_index, ""))
    for house in houses:
        if house["number"] == 6:
            vulnerable_areas.append(BODY_PARTS.get(house["sign_index"], ""))
            break

    # Sun and Mars strength (vitality)
    sun = get_planet_by_name(grahas, "Sun")
    mars = get_planet_by_name(grahas, "Mars")
    sun_strength = get_planet_strength(sun, lagna_index) if sun else None
    mars_strength = get_planet_strength(mars, lagna_index) if mars else None

    vitality_score = 5
    if sun_strength:
        vitality_score += (sun_strength["score"] - 5) * 0.5
    if mars_strength:
        vitality_score += (mars_strength["score"] - 5) * 0.5
    vitality_score = max(1, min(10, round(vitality_score)))

    return {
        "constitution": constitution,
        "lagna_element": lagna_element,
        "sixth_lord": sixth_lord,
        "planets_in_6th": planets_in_6th,
        "eighth_lord": eighth_lord,
        "vulnerable_areas": [a for a in vulnerable_areas if a],
        "vitality_score": vitality_score,
        "sun_strength": sun_strength,
        "mars_strength": mars_strength,
        "disclaimer": DISCLAIMERS["wellness"],
        "summary": f"Constitution: {lagna_element}-based. Vitality score: {vitality_score}/10.",
    }


def calculate_vimshottari_dasha(chart_data: dict, date_of_birth: str) -> dict:
    """Calculate the full Vimshottari Dasha timeline."""
    grahas = chart_data["grahas"]
    moon = get_planet_by_name(grahas, "Moon")

    if not moon:
        return {"error": "Moon position not found in chart data"}

    moon_longitude = moon["longitude"]
    moon_nakshatra_index = moon["nakshatra"]["index"]

    # Step 1: Starting dasha lord from Moon's nakshatra
    starting_lord = NAKSHATRA_DASHA_LORD[moon_nakshatra_index]
    starting_lord_total_years = DASHA_YEARS[starting_lord]

    # Step 2: Calculate elapsed fraction of first dasha
    # Position of Moon within its nakshatra (0 to NAKSHATRA_SPAN)
    nakshatra_start = moon_nakshatra_index * NAKSHATRA_SPAN
    position_in_nakshatra = moon_longitude - nakshatra_start
    fraction_elapsed = position_in_nakshatra / NAKSHATRA_SPAN

    # Remaining years of the first dasha
    remaining_years = starting_lord_total_years * (1 - fraction_elapsed)

    # Step 3: Parse DOB
    dob = datetime.strptime(date_of_birth, "%Y-%m-%d")

    # Step 4: Build Mahadasha timeline
    # Find the index of the starting lord in DASHA_LORDS
    start_index = DASHA_LORDS.index(starting_lord)

    mahadashas = []
    current_date = dob

    for i in range(9):
        lord_index = (start_index + i) % 9
        lord = DASHA_LORDS[lord_index]

        if i == 0:
            duration_years = remaining_years
        else:
            duration_years = DASHA_YEARS[lord]

        duration_days = duration_years * 365.25
        end_date = current_date + timedelta(days=duration_days)

        # Calculate Antardashas within this Mahadasha
        antardashas = _calculate_antardashas(lord, lord_index, current_date, duration_years)

        mahadashas.append({
            "lord": lord,
            "start_date": current_date.strftime("%Y-%m-%d"),
            "end_date": end_date.strftime("%Y-%m-%d"),
            "duration_years": round(duration_years, 2),
            "interpretation": DASHA_INTERPRETATIONS.get(lord, ""),
            "sub_periods": antardashas,
        })

        current_date = end_date

    # Step 5: Find current period
    today = datetime.now()
    current_mahadasha = None
    current_antardasha = None

    for md in mahadashas:
        md_start = datetime.strptime(md["start_date"], "%Y-%m-%d")
        md_end = datetime.strptime(md["end_date"], "%Y-%m-%d")
        if md_start <= today <= md_end:
            current_mahadasha = md
            for ad in md["sub_periods"]:
                ad_start = datetime.strptime(ad["start_date"], "%Y-%m-%d")
                ad_end = datetime.strptime(ad["end_date"], "%Y-%m-%d")
                if ad_start <= today <= ad_end:
                    current_antardasha = ad
                    break
            break

    return {
        "mahadashas": mahadashas,
        "current_mahadasha": current_mahadasha,
        "current_antardasha": current_antardasha,
        "moon_nakshatra": moon["nakshatra"]["name"],
        "starting_dasha_lord": starting_lord,
    }


def _calculate_antardashas(mahadasha_lord: str, mahadasha_lord_index: int,
                           md_start: datetime, md_duration_years: float) -> list[dict]:
    """Calculate Antardasha (sub-periods) within a Mahadasha."""
    antardashas = []
    current_date = md_start

    for i in range(9):
        ad_lord_index = (mahadasha_lord_index + i) % 9
        ad_lord = DASHA_LORDS[ad_lord_index]

        # Antardasha duration = (Mahadasha years * Antardasha lord years) / 120
        ad_duration_years = (md_duration_years * DASHA_YEARS[ad_lord]) / DASHA_TOTAL_YEARS
        ad_duration_days = ad_duration_years * 365.25
        end_date = current_date + timedelta(days=ad_duration_days)

        antardashas.append({
            "lord": ad_lord,
            "start_date": current_date.strftime("%Y-%m-%d"),
            "end_date": end_date.strftime("%Y-%m-%d"),
            "duration_years": round(ad_duration_years, 2),
        })

        current_date = end_date

    return antardashas


def generate_decade_predictions(chart_data: dict, date_of_birth: str) -> list[dict]:
    """Generate decade-wise life predictions (ages 1-10 through 91-100)."""
    from app.services.transit_calculator import get_current_positions

    dob = datetime.strptime(date_of_birth, "%Y-%m-%d")
    lagna_index = chart_data["lagna"]["rashi"]["index"]
    grahas = chart_data["grahas"]

    # Get Moon rashi for Sade Sati detection
    moon = get_planet_by_name(grahas, "Moon")
    moon_rashi = moon["rashi"]["index"] if moon else 0

    # Calculate full dasha timeline
    dasha_data = calculate_vimshottari_dasha(chart_data, date_of_birth)
    mahadashas = dasha_data.get("mahadashas", [])

    # Pre-compute strength of each planet in natal chart
    natal_strengths = {}
    for g in grahas:
        natal_strengths[g["name"]] = get_planet_strength(g, lagna_index)

    decades = []
    for decade_num in range(10):
        age_start = decade_num * 10 + 1
        age_end = (decade_num + 1) * 10
        year_start = dob.year + age_start
        year_end = dob.year + age_end

        decade_start = dob + timedelta(days=age_start * 365.25)
        decade_end = dob + timedelta(days=age_end * 365.25)
        decade_mid = dob + timedelta(days=((age_start + age_end) / 2) * 365.25)

        # 1. Find active Mahadashas for this decade
        primary_dasha = None
        secondary_dasha = None
        dasha_transition = None

        for md in mahadashas:
            md_start = datetime.strptime(md["start_date"], "%Y-%m-%d")
            md_end = datetime.strptime(md["end_date"], "%Y-%m-%d")

            if md_start <= decade_mid <= md_end:
                primary_dasha = md["lord"]

            # Check if a transition happens within the decade
            if decade_start < md_start < decade_end:
                transition_age = age_start + int((md_start - decade_start).days / 365.25)
                dasha_transition = f"{md['lord']} Mahadasha begins at age {transition_age}"
                if primary_dasha and primary_dasha != md["lord"]:
                    secondary_dasha = md["lord"]
                elif not primary_dasha:
                    primary_dasha = md["lord"]

        if not primary_dasha:
            primary_dasha = "Saturn"  # fallback

        # 2. Get transit positions at decade midpoint
        transit_effects = []
        sade_sati = False

        try:
            positions = get_current_positions(decade_mid)
            transit_grahas = {g["name"]: g for g in positions.get("grahas", [])}

            # Jupiter transit analysis
            jupiter_t = transit_grahas.get("Jupiter", {})
            if jupiter_t:
                j_house = ((jupiter_t.get("rashi_index", 0) - lagna_index) % 12) + 1
                if j_house in KENDRA_HOUSES:
                    transit_effects.append(TRANSIT_EFFECTS["jupiter_kendra"])
                elif j_house in TRIKONA_HOUSES:
                    transit_effects.append(TRANSIT_EFFECTS["jupiter_trikona"])
                elif j_house in TRIK_HOUSES:
                    transit_effects.append(TRANSIT_EFFECTS["jupiter_trik"])

            # Saturn transit — Sade Sati detection
            saturn_t = transit_grahas.get("Saturn", {})
            if saturn_t:
                s_rashi = saturn_t.get("rashi_index", 0)
                dist_from_moon = (s_rashi - moon_rashi) % 12
                if dist_from_moon in {11, 0, 1}:  # 12th, 1st, 2nd from Moon
                    sade_sati = True
                    transit_effects.append(TRANSIT_EFFECTS["saturn_sade_sati"])
                elif dist_from_moon == 7:  # 8th from Moon
                    transit_effects.append(TRANSIT_EFFECTS["saturn_ashtama"])
                s_house = ((s_rashi - lagna_index) % 12) + 1
                if s_house in KENDRA_HOUSES:
                    transit_effects.append(TRANSIT_EFFECTS["saturn_kendra"])

            # Rahu/Ketu transit
            rahu_t = transit_grahas.get("Rahu", {})
            if rahu_t:
                r_house = ((rahu_t.get("rashi_index", 0) - lagna_index) % 12) + 1
                if r_house == 1:
                    transit_effects.append(TRANSIT_EFFECTS["rahu_lagna"])
                elif r_house == 7:
                    transit_effects.append(TRANSIT_EFFECTS["rahu_seventh"])

            ketu_t = transit_grahas.get("Ketu", {})
            if ketu_t:
                k_house = ((ketu_t.get("rashi_index", 0) - lagna_index) % 12) + 1
                if k_house in {9, 12}:
                    transit_effects.append(TRANSIT_EFFECTS["ketu_spiritual"])
                elif k_house == 1:
                    transit_effects.append(TRANSIT_EFFECTS["ketu_lagna"])

        except Exception:
            pass  # Transit data unavailable — proceed with Dasha-only analysis

        # 3. Determine strength level of primary Dasha lord
        lord_strength = natal_strengths.get(primary_dasha, {"score": 5})
        score = lord_strength["score"]

        if score >= 7:
            strength_level = "strong"
        elif score <= 3:
            strength_level = "weak"
        else:
            strength_level = "moderate"

        # 4. Score each life area
        lord_predictions = DECADE_PREDICTIONS.get(primary_dasha, DECADE_PREDICTIONS["Saturn"])
        areas = {}
        area_scores = []

        for area in ["career", "relationships", "finance", "health", "spiritual"]:
            area_text = lord_predictions.get(area, {}).get(strength_level, "")
            area_score = _decade_area_score(area, strength_level, sade_sati, primary_dasha)
            areas[area] = {"score": area_score, "prediction": area_text}
            area_scores.append(area_score)

        overall_score = round(sum(area_scores) / len(area_scores)) if area_scores else 3
        overall_score = max(1, min(5, overall_score))

        # Sade Sati penalty on overall score
        if sade_sati:
            overall_score = max(1, overall_score - 1)

        # 5. Generate summary
        summary = (
            f"Ages {age_start}-{age_end}: Primarily influenced by {primary_dasha} Mahadasha"
            + (f" with transition to {secondary_dasha}" if secondary_dasha else "")
            + f". {DASHA_INTERPRETATIONS.get(primary_dasha, '')}"
        )

        decades.append({
            "age_start": age_start,
            "age_end": age_end,
            "year_start": str(year_start),
            "year_end": str(year_end),
            "overall_score": overall_score,
            "primary_dasha": primary_dasha,
            "secondary_dasha": secondary_dasha,
            "dasha_transition": dasha_transition,
            "areas": areas,
            "transit_effects": transit_effects[:3],  # Limit to top 3
            "sade_sati": sade_sati,
            "summary": summary,
        })

    return decades


def _decade_area_score(area: str, strength_level: str, sade_sati: bool, dasha_lord: str) -> int:
    """Compute a 1-5 score for a life area based on Dasha lord strength and transits."""
    base = {"strong": 4, "moderate": 3, "weak": 2}[strength_level]

    # Natural significator bonuses
    if area == "career" and dasha_lord in {"Sun", "Saturn", "Mars"}:
        base += 0.5
    elif area == "relationships" and dasha_lord in {"Venus", "Moon"}:
        base += 0.5
    elif area == "finance" and dasha_lord in {"Jupiter", "Venus", "Mercury"}:
        base += 0.5
    elif area == "health" and dasha_lord in {"Sun", "Mars", "Jupiter"}:
        base += 0.5
    elif area == "spiritual" and dasha_lord in {"Ketu", "Jupiter", "Moon"}:
        base += 0.5

    # Sade Sati penalty for health and career
    if sade_sati:
        if area in {"health", "career"}:
            base -= 1
        elif area == "spiritual":
            base += 0.5  # Sade Sati can deepen spirituality

    return max(1, min(5, round(base)))


def generate_full_analysis(chart_data: dict, date_of_birth: str) -> dict:
    """Generate the complete chart analysis."""
    personality = analyze_personality(chart_data)
    career = analyze_career(chart_data)
    marriage = analyze_marriage(chart_data)
    finance = analyze_finance(chart_data)
    wellness = analyze_wellness(chart_data)
    dasha = calculate_vimshottari_dasha(chart_data, date_of_birth)
    decades = generate_decade_predictions(chart_data, date_of_birth)

    return {
        "personality": personality,
        "career": career,
        "marriage": marriage,
        "finance": finance,
        "wellness": wellness,
        "dasha": dasha,
        "decades": decades,
        "engine_version": ENGINE_VERSION,
        "disclaimer": DISCLAIMERS["general"],
    }


def generate_free_summary(full_analysis: dict) -> dict:
    """Extract only the free-tier fields from a full analysis."""
    personality = full_analysis["personality"]
    marriage = full_analysis["marriage"]
    finance = full_analysis["finance"]
    wellness = full_analysis["wellness"]
    dasha = full_analysis["dasha"]
    career = full_analysis["career"]

    return {
        "personality": {
            "lagna_rashi": personality["lagna_rashi"],
            "lagna_rashi_index": personality["lagna_rashi_index"],
            "moon_sign": personality["moon_sign"],
            "dominant_element": personality["dominant_element"],
            "summary": personality["summary"],
            "lagna_traits": personality["lagna_traits"],
        },
        "career": {
            "tenth_house_lord": career["tenth_house_lord"],
            "tenth_lord_house": career["tenth_lord_house"],
            "summary": career["summary"],
        },
        "marriage": {
            "manglik_status": marriage["manglik_status"],
            "seventh_house_lord": marriage["seventh_house_lord"],
        },
        "finance": {
            "wealth_potential": finance["wealth_potential"],
            "second_lord": finance["second_lord"],
            "eleventh_lord": finance["eleventh_lord"],
        },
        "wellness": {
            "vitality_score": wellness["vitality_score"],
        },
        "dasha": {
            "current_mahadasha": dasha.get("current_mahadasha"),
        },
        "decades": full_analysis.get("decades", [])[:3],  # First 3 decades free
        "engine_version": full_analysis["engine_version"],
        "disclaimer": full_analysis["disclaimer"],
    }


# --- Private helpers ---

def _element_description(element: str) -> str:
    descriptions = {
        "Fire": "Fire-dominant charts suggest passion, leadership, and initiative. You may be action-oriented with strong willpower.",
        "Earth": "Earth-dominant charts suggest practicality, stability, and material awareness. You may be grounded with strong building abilities.",
        "Air": "Air-dominant charts suggest intellectual activity, communication, and social connection. You may be idea-driven with strong analytical skills.",
        "Water": "Water-dominant charts suggest emotional depth, intuition, and sensitivity. You may be empathetic with strong creative instincts.",
    }
    return descriptions.get(element, "")


def _ordinal(n: int | None) -> str:
    if n is None:
        return "unknown"
    suffixes = {1: "1st", 2: "2nd", 3: "3rd"}
    if n in suffixes:
        return suffixes[n]
    return f"{n}th"
