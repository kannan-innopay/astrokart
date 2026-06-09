"""
Ashtakoota compatibility matching engine.

Computes the 8-factor compatibility score (out of 36) from
two persons' Moon nakshatra and rashi positions.
"""

from app.services.astro_constants import FRIENDSHIP_TABLE, SIGN_LORDS

# Varna (spiritual development) — 4 groups cycling through 27 nakshatras
# Brahmin(3), Kshatriya(2), Vaishya(1), Shudra(0)
NAKSHATRA_VARNA = [
    2, 0, 3, 0, 1, 3, 1, 2, 0, 2, 0, 3, 1, 3, 1, 2, 0, 2, 0, 3, 1, 3, 1, 2, 0, 2, 0
]

# Vashya (dominance) — based on Moon rashi
# Categories: Chatushpada(0), Manava(1), Jalachara(2), Vanachara(3), Keeta(4)
RASHI_VASHYA = [
    0, 0, 1, 2, 3, 1, 1, 4, 0, 0, 1, 2
]

VASHYA_COMPATIBILITY = {
    (0, 0): 2, (0, 1): 1, (0, 2): 0, (0, 3): 1, (0, 4): 0,
    (1, 0): 1, (1, 1): 2, (1, 2): 0, (1, 3): 0, (1, 4): 0,
    (2, 0): 0, (2, 1): 0, (2, 2): 2, (2, 3): 0, (2, 4): 1,
    (3, 0): 1, (3, 1): 0, (3, 2): 0, (3, 3): 2, (3, 4): 0,
    (4, 0): 0, (4, 1): 0, (4, 2): 1, (4, 3): 0, (4, 4): 2,
}

# Yoni (animal compatibility) — 14 animals cycling through 27 nakshatras
NAKSHATRA_YONI = [
    0, 1, 2, 3, 3, 4, 5, 6, 5, 7, 8, 9, 9, 10, 9, 10, 11, 11, 4, 8, 8, 8, 12, 0, 12, 9, 1
]
# 0=Horse, 1=Elephant, 2=Sheep, 3=Snake, 4=Dog, 5=Cat, 6=Ram, 7=Rat,
# 8=Cow, 9=Buffalo, 10=Tiger, 11=Hare, 12=Monkey

# Enemy animals (mutual enmity)
YONI_ENEMIES = {
    (0, 9), (9, 0),   # Horse-Buffalo
    (1, 12), (12, 1), # Elephant-Monkey (actually Lion, simplified)
    (2, 8), (8, 2),   # Sheep-? (simplified)
    (3, 8), (8, 3),   # Snake-Mongoose (simplified to Cow)
    (4, 11), (11, 4), # Dog-Hare
    (5, 7), (7, 5),   # Cat-Rat
    (6, 8), (8, 6),   # Ram-? (simplified)
    (10, 9), (9, 10), # Tiger-Buffalo (actually Cow)
}

# Gana (temperament) — Deva(0), Manushya(1), Rakshasa(2)
NAKSHATRA_GANA = [
    0, 1, 2, 0, 0, 1, 0, 0, 2, 2, 1, 0, 0, 2, 0, 2, 0, 2, 2, 1, 1, 0, 2, 2, 1, 1, 0
]

GANA_SCORE = {
    (0, 0): 6, (0, 1): 5, (0, 2): 1,
    (1, 0): 5, (1, 1): 6, (1, 2): 0,
    (2, 0): 1, (2, 1): 0, (2, 2): 6,
}

# Nadi (health/physiological) — Aadi(0), Madhya(1), Antya(2)
NAKSHATRA_NADI = [
    0, 1, 2, 2, 1, 0, 0, 1, 2, 2, 1, 0, 0, 1, 2, 2, 1, 0, 0, 1, 2, 2, 1, 0, 0, 1, 2
]


def calculate_compatibility(
    person1_moon_nakshatra: int,
    person1_moon_rashi: int,
    person2_moon_nakshatra: int,
    person2_moon_rashi: int,
) -> dict:
    """
    Calculate 8-factor Ashtakoota compatibility score.

    Args:
        person1_moon_nakshatra: Moon nakshatra index (0-26) of person 1
        person1_moon_rashi: Moon rashi index (0-11) of person 1
        person2_moon_nakshatra: Moon nakshatra index (0-26) of person 2
        person2_moon_rashi: Moon rashi index (0-11) of person 2

    Returns:
        Dict with total_score, max_score, factors breakdown, and interpretation
    """
    factors = []

    # 1. Varna (max 1)
    v1 = NAKSHATRA_VARNA[person1_moon_nakshatra % 27]
    v2 = NAKSHATRA_VARNA[person2_moon_nakshatra % 27]
    varna_score = 1 if v1 >= v2 else 0
    factors.append({
        "name": "Varna",
        "name_local": "Varna (Spiritual)",
        "score": varna_score,
        "max": 1,
        "description": "Measures spiritual compatibility and ego levels." + (" Compatible." if varna_score > 0 else " May need understanding."),
    })

    # 2. Vashya (max 2)
    vs1 = RASHI_VASHYA[person1_moon_rashi % 12]
    vs2 = RASHI_VASHYA[person2_moon_rashi % 12]
    vashya_score = VASHYA_COMPATIBILITY.get((vs1, vs2), 0)
    factors.append({
        "name": "Vashya",
        "name_local": "Vashya (Dominance)",
        "score": vashya_score,
        "max": 2,
        "description": "Measures mutual attraction and control dynamics." + (f" Score: {vashya_score}/2."),
    })

    # 3. Tara (max 3)
    tara_diff = (person2_moon_nakshatra - person1_moon_nakshatra) % 27
    tara_group = (tara_diff % 9) + 1
    # Favorable: 1(Janma-neutral), 2(Sampat), 4(Kshema), 6(Sadhana), 8(Mitra), 9(Ati-Mitra)
    if tara_group in {2, 4, 6, 8, 9}:
        tara_score = 3
    elif tara_group in {1}:
        tara_score = 1.5
    else:
        tara_score = 0
    factors.append({
        "name": "Tara",
        "name_local": "Tara (Birth Star)",
        "score": tara_score,
        "max": 3,
        "description": "Measures destiny and health compatibility based on star distance.",
    })

    # 4. Yoni (max 4)
    y1 = NAKSHATRA_YONI[person1_moon_nakshatra % 27]
    y2 = NAKSHATRA_YONI[person2_moon_nakshatra % 27]
    if y1 == y2:
        yoni_score = 4
    elif (y1, y2) in YONI_ENEMIES or (y2, y1) in YONI_ENEMIES:
        yoni_score = 0
    else:
        yoni_score = 2
    factors.append({
        "name": "Yoni",
        "name_local": "Yoni (Nature)",
        "score": yoni_score,
        "max": 4,
        "description": "Measures physical and intimate compatibility.",
    })

    # 5. Graha Maitri (max 5)
    lord1 = SIGN_LORDS.get(person1_moon_rashi, "Sun")
    lord2 = SIGN_LORDS.get(person2_moon_rashi, "Sun")
    if lord1 == lord2:
        maitri_score = 5
    else:
        friendship1 = FRIENDSHIP_TABLE.get(lord1, {})
        friendship2 = FRIENDSHIP_TABLE.get(lord2, {})
        is_friend_1to2 = lord2 in friendship1.get("friends", [])
        is_friend_2to1 = lord1 in friendship2.get("friends", [])
        is_enemy_1to2 = lord2 in friendship1.get("enemies", [])
        is_enemy_2to1 = lord1 in friendship2.get("enemies", [])

        if is_friend_1to2 and is_friend_2to1:
            maitri_score = 5
        elif is_friend_1to2 or is_friend_2to1:
            maitri_score = 3
        elif is_enemy_1to2 and is_enemy_2to1:
            maitri_score = 0
        elif is_enemy_1to2 or is_enemy_2to1:
            maitri_score = 1
        else:
            maitri_score = 3  # neutral
    factors.append({
        "name": "Graha Maitri",
        "name_local": "Graha Maitri (Planetary Friendship)",
        "score": maitri_score,
        "max": 5,
        "description": "Measures mental compatibility through Moon sign lord friendship.",
    })

    # 6. Gana (max 6)
    g1 = NAKSHATRA_GANA[person1_moon_nakshatra % 27]
    g2 = NAKSHATRA_GANA[person2_moon_nakshatra % 27]
    gana_score = GANA_SCORE.get((g1, g2), 0)
    gana_names = {0: "Deva", 1: "Manushya", 2: "Rakshasa"}
    factors.append({
        "name": "Gana",
        "name_local": "Gana (Temperament)",
        "score": gana_score,
        "max": 6,
        "description": f"Measures temperament match. Person 1: {gana_names.get(g1, '?')}, Person 2: {gana_names.get(g2, '?')}.",
    })

    # 7. Bhakoota (max 7)
    rashi_diff = (person2_moon_rashi - person1_moon_rashi) % 12
    # Favorable: 1/1, 2/12, 3/11, 4/10, 5/9, 6/8, 7/7
    favorable_diffs = {0, 1, 2, 3, 4, 5, 6, 10, 11}
    # Unfavorable: 6/8 (special case)
    if rashi_diff in {5, 7}:  # 6th and 8th from each other
        bhakoota_score = 0
    elif rashi_diff in favorable_diffs:
        bhakoota_score = 7
    else:
        bhakoota_score = 0
    factors.append({
        "name": "Bhakoota",
        "name_local": "Bhakoota (Mutual Influence)",
        "score": bhakoota_score,
        "max": 7,
        "description": "Measures influence on each other's health, wealth, and happiness.",
    })

    # 8. Nadi (max 8)
    n1 = NAKSHATRA_NADI[person1_moon_nakshatra % 27]
    n2 = NAKSHATRA_NADI[person2_moon_nakshatra % 27]
    nadi_score = 0 if n1 == n2 else 8
    nadi_names = {0: "Aadi (Vata)", 1: "Madhya (Pitta)", 2: "Antya (Kapha)"}
    factors.append({
        "name": "Nadi",
        "name_local": "Nadi (Health)",
        "score": nadi_score,
        "max": 8,
        "description": f"Measures health and genetic compatibility. Person 1: {nadi_names.get(n1, '?')}, Person 2: {nadi_names.get(n2, '?')}."
        + (" Same Nadi — considered unfavorable in tradition." if nadi_score == 0 else " Different Nadi — favorable."),
    })

    total_score = sum(f["score"] for f in factors)
    max_score = 36

    # Interpretation
    if total_score >= 30:
        interpretation = "Excellent compatibility. The charts suggest a very strong and harmonious bond."
    elif total_score >= 24:
        interpretation = "Very good compatibility. The charts suggest a positive and supportive relationship."
    elif total_score >= 18:
        interpretation = "Good compatibility. The relationship has a solid foundation with some areas to be mindful of."
    elif total_score >= 12:
        interpretation = "Moderate compatibility. Some challenges may arise that require understanding and effort."
    else:
        interpretation = "Below average compatibility from this traditional analysis. This is one perspective — personal connection, mutual respect, and effort matter greatly."

    return {
        "total_score": total_score,
        "max_score": max_score,
        "factors": factors,
        "interpretation": interpretation,
        "disclaimer": "Ashtakoota matching is one traditional tool among many. It should not be the sole basis for marriage decisions. Personal compatibility, mutual respect, and family harmony are equally important.",
    }
