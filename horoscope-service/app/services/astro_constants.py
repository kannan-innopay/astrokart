"""
Vedic astrology constants, text banks, and lookup tables.

All interpretive text uses suggestive language ("suggests", "may", "tends to")
rather than deterministic claims, per content safety guidelines.
"""

# --- Planetary rulerships ---

SIGN_LORDS = {
    0: "Mars", 1: "Venus", 2: "Mercury", 3: "Moon",
    4: "Sun", 5: "Mercury", 6: "Venus", 7: "Mars",
    8: "Jupiter", 9: "Saturn", 10: "Saturn", 11: "Jupiter",
}

EXALTATION = {
    "Sun": 0, "Moon": 1, "Mars": 9, "Mercury": 5,
    "Jupiter": 3, "Venus": 11, "Saturn": 6,
}

DEBILITATION = {
    "Sun": 6, "Moon": 7, "Mars": 3, "Mercury": 11,
    "Jupiter": 9, "Venus": 5, "Saturn": 0,
}

OWN_SIGNS = {
    "Sun": [4],
    "Moon": [3],
    "Mars": [0, 7],
    "Mercury": [2, 5],
    "Jupiter": [8, 11],
    "Venus": [1, 6],
    "Saturn": [9, 10],
    "Rahu": [10],
    "Ketu": [7],
}

NATURAL_BENEFICS = {"Jupiter", "Venus", "Moon", "Mercury"}
NATURAL_MALEFICS = {"Sun", "Mars", "Saturn", "Rahu", "Ketu"}

KENDRA_HOUSES = {1, 4, 7, 10}
TRIKONA_HOUSES = {1, 5, 9}
TRIK_HOUSES = {6, 8, 12}
UPACHAYA_HOUSES = {3, 6, 10, 11}

# Natural friendships between planets
FRIENDSHIP_TABLE = {
    "Sun": {"friends": ["Moon", "Mars", "Jupiter"], "enemies": ["Venus", "Saturn"], "neutral": ["Mercury"]},
    "Moon": {"friends": ["Sun", "Mercury"], "enemies": [], "neutral": ["Mars", "Jupiter", "Venus", "Saturn"]},
    "Mars": {"friends": ["Sun", "Moon", "Jupiter"], "enemies": ["Mercury"], "neutral": ["Venus", "Saturn"]},
    "Mercury": {"friends": ["Sun", "Venus"], "enemies": ["Moon"], "neutral": ["Mars", "Jupiter", "Saturn"]},
    "Jupiter": {"friends": ["Sun", "Moon", "Mars"], "enemies": ["Mercury", "Venus"], "neutral": ["Saturn"]},
    "Venus": {"friends": ["Mercury", "Saturn"], "enemies": ["Sun", "Moon"], "neutral": ["Mars", "Jupiter"]},
    "Saturn": {"friends": ["Mercury", "Venus"], "enemies": ["Sun", "Moon", "Mars"], "neutral": ["Jupiter"]},
    "Rahu": {"friends": ["Mercury", "Venus", "Saturn"], "enemies": ["Sun", "Moon", "Mars"], "neutral": ["Jupiter"]},
    "Ketu": {"friends": ["Mars", "Jupiter"], "enemies": ["Mercury", "Venus"], "neutral": ["Sun", "Moon", "Saturn"]},
}

# --- Rashi-to-element mapping ---

RASHI_ELEMENTS = {
    0: "Fire", 1: "Earth", 2: "Air", 3: "Water",
    4: "Fire", 5: "Earth", 6: "Air", 7: "Water",
    8: "Fire", 9: "Earth", 10: "Air", 11: "Water",
}

RASHI_QUALITIES = {
    0: "Cardinal", 1: "Fixed", 2: "Mutable", 3: "Cardinal",
    4: "Fixed", 5: "Mutable", 6: "Cardinal", 7: "Fixed",
    8: "Mutable", 9: "Cardinal", 10: "Fixed", 11: "Mutable",
}

# --- Body parts per Rashi (for wellness analysis) ---

BODY_PARTS = {
    0: "Head, brain, face",
    1: "Throat, neck, vocal cords",
    2: "Shoulders, arms, lungs",
    3: "Chest, stomach, breasts",
    4: "Heart, spine, upper back",
    5: "Intestines, digestive system",
    6: "Kidneys, lower back, skin",
    7: "Reproductive organs, bladder",
    8: "Hips, thighs, liver",
    9: "Knees, joints, bones",
    10: "Ankles, calves, circulation",
    11: "Feet, lymphatic system, immunity",
}

# --- Personality text banks ---

RASHI_PERSONALITY = {
    0: "You tend to be pioneering, courageous, and action-oriented. Your chart suggests strong leadership qualities with an innate drive to initiate new ventures. There may be a natural assertiveness and competitive spirit in your approach to life.",
    1: "Your chart suggests a grounded, patient, and determined nature. You may value stability, comfort, and material security. There is often an appreciation for beauty, art, and the finer things in life.",
    2: "Your personality tends toward intellectual curiosity, adaptability, and strong communication skills. You may have diverse interests and a natural ability to connect ideas and people. Mental stimulation is likely important to you.",
    3: "Your chart indicates a nurturing, intuitive, and emotionally sensitive nature. Home, family, and emotional security may be central themes. You tend to be protective of loved ones and deeply connected to your roots.",
    4: "You tend to be confident, creative, and drawn to self-expression. Your chart suggests natural charisma and leadership ability. There may be a generous spirit with a desire for recognition and appreciation.",
    5: "Your chart suggests an analytical, detail-oriented, and service-minded nature. You may have strong practical skills and a desire for order and improvement. Health consciousness and helping others tend to be important themes.",
    6: "Your personality may lean toward diplomacy, partnership, and a strong sense of fairness. You tend to value harmony in relationships and may have refined aesthetic sensibilities. Balance and justice are likely important to you.",
    7: "Your chart indicates intensity, depth, and a transformative nature. You may possess strong intuition and emotional resilience. There tends to be a capacity for deep investigation and an interest in life's mysteries.",
    8: "You tend to be optimistic, philosophical, and drawn to exploration — both physical and intellectual. Your chart suggests a love of freedom, higher learning, and expanding horizons. Wisdom and teaching may be natural strengths.",
    9: "Your chart suggests discipline, ambition, and a methodical approach to goals. You may have strong organizational skills and patience for long-term planning. Responsibility and structure tend to be important themes.",
    10: "Your personality tends toward innovation, independence, and humanitarian ideals. You may think unconventionally and value intellectual freedom. Community and social causes may be important to you.",
    11: "Your chart indicates compassion, imagination, and spiritual sensitivity. You may have strong creative and intuitive gifts. There tends to be a deep empathy for others and a connection to the transcendent.",
}

RASHI_MOON_NATURE = {
    0: "Emotionally direct and impulsive. Your feelings tend to be intense but may not linger long. You may process emotions through action and physical activity.",
    1: "Emotionally steady and comfort-seeking. Your feelings tend to be deep and stable. Change may feel unsettling, and you may find solace in familiar routines.",
    2: "Emotionally expressive and mentally active. You may process feelings through conversation and intellectual analysis. Variety in emotional experience tends to appeal to you.",
    3: "Deeply emotional and highly intuitive. Your feelings tend to be strong and changeable, like the tides. Nurturing and being nurtured may be core emotional needs.",
    4: "Emotionally warm and generous. You may need recognition and appreciation to feel emotionally fulfilled. Creative expression can be an important emotional outlet.",
    5: "Emotionally reserved and analytical. You may process feelings through practical action and problem-solving. Being of service to others can bring emotional satisfaction.",
    6: "Emotionally attuned to harmony and relationships. You may feel unsettled by conflict and seek peace in partnerships. Aesthetic beauty can uplift your emotional state.",
    7: "Emotionally intense and private. Your feelings tend to run very deep, and you may guard your inner world carefully. Emotional transformation and depth are likely recurring themes.",
    8: "Emotionally optimistic and freedom-loving. You may process feelings through philosophical understanding and adventure. Emotional growth often comes through exploration and learning.",
    9: "Emotionally controlled and responsible. You may tend to suppress feelings in favor of duty. Emotional fulfillment often comes through achievement and building lasting structures.",
    10: "Emotionally independent and somewhat detached. You may process feelings intellectually and value emotional freedom. Group connections can be deeply fulfilling.",
    11: "Emotionally sensitive and deeply empathetic. You may absorb others' feelings easily and need solitude to recharge. Creative and spiritual practices can help ground your emotions.",
}

# --- Nakshatra traits (27 nakshatras) ---

NAKSHATRA_TRAITS = {
    0: "Ashwini energy suggests initiative, healing ability, and swiftness. You may be naturally drawn to healing arts or fast-paced endeavors.",
    1: "Bharani energy suggests creativity, endurance, and transformation. You may have strong will and the ability to nurture new beginnings.",
    2: "Krittika energy suggests sharpness, purification, and courage. You may possess a cutting intellect and the drive to refine and perfect.",
    3: "Rohini energy suggests creativity, beauty, and material abundance. You may have strong aesthetic sense and charm.",
    4: "Mrigashira energy suggests curiosity, searching, and gentle nature. You may be naturally exploratory with a love of knowledge.",
    5: "Ardra energy suggests intensity, transformation, and intellectual power. You may experience significant turning points that lead to growth.",
    6: "Punarvasu energy suggests renewal, optimism, and return to goodness. You may have resilience and the ability to bounce back.",
    7: "Pushya energy suggests nourishment, generosity, and spiritual depth. You may be naturally supportive and community-minded.",
    8: "Ashlesha energy suggests mysticism, intensity, and psychological depth. You may have strong intuition and penetrating insight.",
    9: "Magha energy suggests authority, ancestral connection, and nobility. You may carry a sense of dignity and respect for tradition.",
    10: "Purva Phalguni energy suggests creativity, romance, and enjoyment. You may have artistic talent and a love of leisure and beauty.",
    11: "Uttara Phalguni energy suggests friendship, generosity, and social grace. You may be naturally helpful with strong partnerships.",
    12: "Hasta energy suggests skill, craftsmanship, and dexterity. You may excel in detailed work requiring precision and manual ability.",
    13: "Chitra energy suggests brilliance, beauty, and creative vision. You may have a strong desire to create something beautiful and lasting.",
    14: "Swati energy suggests independence, flexibility, and diplomacy. You may value personal freedom while maintaining graceful social skills.",
    15: "Vishakha energy suggests determination, focus, and single-pointed purpose. You may pursue goals with unwavering resolve.",
    16: "Anuradha energy suggests devotion, friendship, and organizational skill. You may excel in building and maintaining deep connections.",
    17: "Jyeshtha energy suggests seniority, protective strength, and authority. You may naturally assume leadership and guardian roles.",
    18: "Mula energy suggests investigation, transformation, and getting to the root. You may be drawn to uncovering hidden truths.",
    19: "Purva Ashadha energy suggests invincibility, enthusiasm, and influence. You may have persuasive abilities and philosophical depth.",
    20: "Uttara Ashadha energy suggests lasting achievement, integrity, and leadership. You may build things of enduring value.",
    21: "Shravana energy suggests listening, learning, and wisdom. You may have strong receptive abilities and a talent for acquiring knowledge.",
    22: "Dhanishta energy suggests wealth, musical talent, and adaptability. You may have rhythmic abilities and material prosperity potential.",
    23: "Shatabhisha energy suggests healing, seclusion, and mystical abilities. You may be drawn to alternative healing or scientific research.",
    24: "Purva Bhadrapada energy suggests intensity, spiritual fire, and transformation. You may undergo significant spiritual evolution.",
    25: "Uttara Bhadrapada energy suggests depth, wisdom, and controlled power. You may have deep inner strength and spiritual maturity.",
    26: "Revati energy suggests nurturing, safe travel, and spiritual completion. You may be naturally compassionate with a desire to protect others.",
}

# --- Planet-in-house effects (brief texts) ---

PLANET_IN_HOUSE = {
    "Sun": {
        1: "Strong sense of self, natural authority, leadership presence",
        2: "Focus on family values and speech, potential for wealth through authority",
        3: "Courageous communication, strong relationship with siblings",
        4: "Connection to homeland and parents, inner confidence",
        5: "Creative intelligence, possible leadership in education or politics",
        6: "Ability to overcome enemies and obstacles, service orientation",
        7: "Focus on partnerships, spouse may have authoritative nature",
        8: "Interest in research, transformation, and hidden matters",
        9: "Philosophical nature, connection with father, love of dharma",
        10: "Strong career drive, public recognition, government connection",
        11: "Gains through authority figures, fulfillment of desires",
        12: "Spiritual inclination, possible foreign connections, self-reflection",
    },
    "Moon": {
        1: "Emotional sensitivity, changeable nature, public appeal",
        2: "Family attachment, wealth fluctuations, sweet speech",
        3: "Courage through emotional intelligence, creative communication",
        4: "Deep emotional roots, attachment to home and mother",
        5: "Creative mind, emotional intelligence, love of learning",
        6: "Emotional service orientation, may tend to worry about health",
        7: "Strong need for partnership, emotionally attuned to others",
        8: "Emotional depth, intuitive abilities, interest in mysteries",
        9: "Philosophical emotions, spiritual inclination, love of travel",
        10: "Public emotional connection, career in nurturing fields",
        11: "Social fulfillment, emotional gains through community",
        12: "Rich inner world, spiritual emotions, need for solitude",
    },
    "Mars": {
        1: "Physical energy, assertiveness, competitive nature, athletic ability",
        2: "Direct speech, family protector, drive for financial security",
        3: "Courageous initiative, strong will, entrepreneurial spirit",
        4: "Property interests, strong foundations, protective of home",
        5: "Dynamic creativity, sports talent, passionate romance",
        6: "Ability to defeat competitors, strong in conflicts and service",
        7: "Passionate partnerships, assertive spouse, Manglik consideration",
        8: "Investigative nature, interest in surgery or research",
        9: "Active pursuit of philosophy, adventurous travels",
        10: "Career drive, leadership in action-oriented fields",
        11: "Achievement of ambitions, gains through courage",
        12: "Spiritual warrior energy, expenses on property",
    },
    "Mercury": {
        1: "Quick wit, strong communication skills, youthful appearance",
        2: "Eloquent speech, business acumen, intellectual family",
        3: "Writing talent, skilled in media and communication",
        4: "Intellectual home environment, analytical mind",
        5: "Sharp intellect, skill in education, analytical creativity",
        6: "Problem-solving ability, analytical approach to health",
        7: "Business partnerships, communicative spouse",
        8: "Research aptitude, interest in occult sciences",
        9: "Love of learning, multiple areas of knowledge",
        10: "Career in communication, business, or education",
        11: "Intellectual social circle, gains through networking",
        12: "Intuitive intellect, interest in spiritual knowledge",
    },
    "Jupiter": {
        1: "Wisdom, optimism, generous nature, respected personality",
        2: "Wealth potential, wise speech, family prosperity",
        3: "Broad communication, philosophical writing",
        4: "Comfortable home, vehicles, happiness from education",
        5: "Strong intellect, good fortune with children, spiritual wisdom",
        6: "Ability to overcome obstacles through wisdom",
        7: "Wise and supportive spouse, beneficial partnerships",
        8: "Longevity, interest in mystical knowledge, inheritance",
        9: "Strong dharma, philosophical nature, fortunate journeys",
        10: "Career success, respected profession, ethical leadership",
        11: "Fulfillment of desires, gains from wisdom",
        12: "Spiritual liberation focus, charitable nature",
    },
    "Venus": {
        1: "Attractive personality, artistic sensibility, love of beauty",
        2: "Wealth through luxury goods, sweet speech, family harmony",
        3: "Artistic communication, creative talents",
        4: "Comfortable lifestyle, beautiful home, vehicle comforts",
        5: "Romantic nature, creative arts talent, love of entertainment",
        6: "Service in beauty or health fields, diplomatic conflict resolution",
        7: "Strong marriage potential, harmonious partnerships",
        8: "Hidden wealth, transformative relationships",
        9: "Love of culture, philosophical appreciation of beauty",
        10: "Career in arts, luxury, or diplomacy",
        11: "Social popularity, gains through creative ventures",
        12: "Spiritual love, foreign luxury, expenses on pleasures",
    },
    "Saturn": {
        1: "Disciplined personality, serious demeanor, slow but steady growth",
        2: "Careful with finances, structured speech, delayed family matters",
        3: "Persistent effort, determined communication",
        4: "Lessons through home and property, late-life comforts",
        5: "Serious intellectual pursuits, disciplined creativity",
        6: "Strong ability to overcome long-term challenges",
        7: "Committed partnerships, older or mature spouse tendency",
        8: "Interest in longevity, structured approach to transformation",
        9: "Disciplined spiritual practice, respect for tradition",
        10: "Career through hard work, delayed but lasting success",
        11: "Gains through perseverance, older social connections",
        12: "Spiritual discipline, possible isolation periods, deep meditation",
    },
    "Rahu": {
        1: "Unconventional personality, strong worldly desires, unique path",
        2: "Unusual speech patterns, foreign wealth sources",
        3: "Bold communication, interest in technology and media",
        4: "Foreign connections to home, unusual living situations",
        5: "Innovative thinking, unconventional creativity",
        6: "Ability to overcome unusual obstacles",
        7: "Attraction to foreign or unconventional partners",
        8: "Deep research abilities, interest in occult matters",
        9: "Unorthodox philosophy, foreign spiritual traditions",
        10: "Career in technology, foreign companies, or unconventional fields",
        11: "Gains through networking, technology, or foreign connections",
        12: "Foreign residence potential, unusual spiritual experiences",
    },
    "Ketu": {
        1: "Spiritual personality, detached from material identity",
        2: "Indifference to wealth accumulation, unusual speech",
        3: "Intuitive communication, spiritual courage",
        4: "Detachment from material comforts, inner peace seeking",
        5: "Intuitive intelligence, past-life wisdom, spiritual creativity",
        6: "Natural ability to overcome enemies, spiritual service",
        7: "Lessons in relationships, spiritual partnerships",
        8: "Strong mystical abilities, interest in liberation",
        9: "Deep spiritual wisdom, pilgrimage inclination",
        10: "Unconventional career path, spiritual vocation",
        11: "Selective social connections, spiritual gains",
        12: "Strong moksha potential, natural meditation ability, spiritual liberation",
    },
}

# --- Career recommendations per dominant planet ---

CAREER_FIELDS = {
    "Sun": ["Government and civil services", "Politics and leadership", "Medicine and healthcare", "Administrative roles", "Management and authority positions"],
    "Moon": ["Nursing and caregiving", "Hospitality and food industry", "Public relations", "Psychology and counseling", "Water and marine industries"],
    "Mars": ["Engineering and technology", "Military and defense", "Sports and athletics", "Surgery and medical procedures", "Real estate and construction"],
    "Mercury": ["Business and commerce", "Writing and journalism", "IT and software development", "Accounting and finance", "Teaching and education"],
    "Jupiter": ["Education and academia", "Law and justice", "Banking and financial advisory", "Religious and spiritual roles", "Consultancy and advisory"],
    "Venus": ["Arts and entertainment", "Fashion and beauty", "Interior design and architecture", "Luxury goods and jewelry", "Music and performing arts"],
    "Saturn": ["Mining and agriculture", "Manufacturing and labor", "Legal and judicial services", "Research and archaeology", "Social work and NGOs"],
    "Rahu": ["Technology and innovation", "Foreign trade and exports", "Aviation and travel industry", "Media and film production", "Pharmaceutical and chemical industries"],
    "Ketu": ["Spirituality and healing arts", "Alternative medicine", "Astrology and occult sciences", "Research and investigation", "Monastic and renunciation paths"],
}

# --- Vimshottari Dasha constants ---

# The 9 dasha lords in sequence
DASHA_LORDS = ["Ketu", "Venus", "Sun", "Moon", "Mars", "Rahu", "Jupiter", "Saturn", "Mercury"]

# Duration in years for each lord's Mahadasha
DASHA_YEARS = {
    "Ketu": 7,
    "Venus": 20,
    "Sun": 6,
    "Moon": 10,
    "Mars": 7,
    "Rahu": 18,
    "Jupiter": 16,
    "Saturn": 19,
    "Mercury": 17,
}

DASHA_TOTAL_YEARS = 120  # Sum of all dasha years

# Maps each of the 27 nakshatras to its starting dasha lord
# Cycles: Ketu(0,9,18), Venus(1,10,19), Sun(2,11,20), Moon(3,12,21),
# Mars(4,13,22), Rahu(5,14,23), Jupiter(6,15,24), Saturn(7,16,25), Mercury(8,17,26)
NAKSHATRA_DASHA_LORD = {
    0: "Ketu",      # Ashwini
    1: "Venus",     # Bharani
    2: "Sun",       # Krittika
    3: "Moon",      # Rohini
    4: "Mars",      # Mrigashira
    5: "Rahu",      # Ardra
    6: "Jupiter",   # Punarvasu
    7: "Saturn",    # Pushya
    8: "Mercury",   # Ashlesha
    9: "Ketu",      # Magha
    10: "Venus",    # Purva Phalguni
    11: "Sun",      # Uttara Phalguni
    12: "Moon",     # Hasta
    13: "Mars",     # Chitra
    14: "Rahu",     # Swati
    15: "Jupiter",  # Vishakha
    16: "Saturn",   # Anuradha
    17: "Mercury",  # Jyeshtha
    18: "Ketu",     # Mula
    19: "Venus",    # Purva Ashadha
    20: "Sun",      # Uttara Ashadha
    21: "Moon",     # Shravana
    22: "Mars",     # Dhanishta
    23: "Rahu",     # Shatabhisha
    24: "Jupiter",  # Purva Bhadrapada
    25: "Saturn",   # Uttara Bhadrapada
    26: "Mercury",  # Revati
}

# --- Dasha lord interpretations ---

DASHA_INTERPRETATIONS = {
    "Sun": "This period may bring themes of authority, self-expression, and recognition. Career advancement and connection with father figures could be highlighted.",
    "Moon": "This period may emphasize emotions, nurturing, home life, and public connections. Mental peace and mother-related themes could be prominent.",
    "Mars": "This period may bring energy, courage, and action. Property matters, siblings, and competitive endeavors could be in focus.",
    "Mercury": "This period may highlight intellect, communication, business, and education. Learning, networking, and analytical pursuits could be active.",
    "Jupiter": "This period may bring expansion, wisdom, and spiritual growth. Education, children, wealth, and ethical development could be supported.",
    "Venus": "This period may emphasize relationships, luxury, creativity, and comfort. Marriage, artistic pursuits, and material enjoyment could be prominent.",
    "Saturn": "This period may bring discipline, hard work, and structural changes. Patience, persistence, and karmic lessons could be important themes.",
    "Rahu": "This period may bring worldly ambitions, unconventional experiences, and foreign connections. Material desires and breaking conventions could be highlighted.",
    "Ketu": "This period may emphasize spiritual growth, detachment, and inner transformation. Letting go, mystical experiences, and past-life themes could emerge.",
}

# --- Dhana Yoga conditions (simplified) ---

DHANA_YOGA_DESCRIPTIONS = {
    "lords_2_11_kendra": "Lords of 2nd and 11th houses are positioned in kendras, suggesting strong wealth-building potential.",
    "jupiter_wealth_house": "Jupiter occupies a wealth house (2nd, 5th, 9th, or 11th), suggesting natural fortune and abundance.",
    "venus_exalted_kendra": "Venus is exalted or in own sign in a kendra, indicating potential for luxury and material comfort.",
    "lord_9_in_kendra": "The 9th lord (fortune) is in a kendra, suggesting luck supports your material endeavors.",
    "lords_2_11_mutual": "The 2nd and 11th lords are in mutual kendras, suggesting a strong connection between stored wealth and income.",
}

# --- Manglik assessment texts ---

MANGLIK_TEXT = {
    "manglik": "Mars is placed in a Manglik position in your chart. This is a traditional consideration for marriage compatibility. Many astrologers recommend matching with another Manglik individual or performing specific remedies. This is one of many factors in compatibility assessment.",
    "non_manglik": "Mars is not in a traditional Manglik position in your chart. This is considered favorable for marriage compatibility from this specific perspective.",
    "partial": "Mars is in a position that some traditions consider partially Manglik. The effects may be mitigated by other planetary placements. A detailed compatibility analysis with a qualified astrologer is recommended for marriage-related decisions.",
}

# --- Disclaimer texts ---

DISCLAIMERS = {
    "wellness": "This section is based on traditional Vedic astrology and is for reflection only. It is not medical advice. Please consult a qualified doctor for any health concerns.",
    "finance": "This is not financial advice. The analysis is based on traditional astrological principles. Please consult a qualified financial advisor before making investment decisions.",
    "marriage": "This should be treated as guidance only and is not a substitute for personal judgment, family discussion, or professional counselling.",
    "general": "This analysis is generated using traditional Vedic astrology rules and is intended for personal reflection and entertainment. For important life decisions, please consult qualified professionals and experienced astrologers.",
}

# --- Decade prediction text banks (9 planets × 5 areas × 3 strength levels) ---

DECADE_PREDICTIONS = {
    "Sun": {
        "career": {
            "strong": "This period may bring career recognition, authority roles, and connections with government or leadership positions. Your natural confidence could attract opportunities for advancement and public visibility.",
            "moderate": "Career progress tends to be steady during this phase. Authority themes are present but may require patience and persistent effort to manifest fully.",
            "weak": "Career may face challenges related to ego dynamics or authority conflicts. Developing humility and adaptability could help navigate this period constructively.",
        },
        "relationships": {
            "strong": "Relationships may be warm and generous during this phase. Your charisma and leadership qualities could attract respect and admiration from partners and family.",
            "moderate": "Relationships tend to be stable but may require attention to balance personal ambitions with partner needs. Mutual respect is key.",
            "weak": "Relationship dynamics may be strained by ego or dominance issues. Practicing genuine humility and listening could strengthen bonds.",
        },
        "finance": {
            "strong": "Financial growth through authoritative positions, government roles, or leadership ventures is supported. Inheritance and paternal wealth may also play a role.",
            "moderate": "Finances tend to be stable with gradual growth. Income from professional roles and steady employment is indicated.",
            "weak": "Financial caution is advised. Overconfidence in investments or speculative ventures could lead to setbacks. Conservative management is recommended.",
        },
        "health": {
            "strong": "Vitality and physical energy tend to be strong. Heart health and overall constitution may be robust during this phase.",
            "moderate": "Health is generally stable but attention to heart, eyes, and bones is advisable. Regular health check-ups are recommended.",
            "weak": "Health may need extra attention, particularly regarding heart, eyes, and vitality. Stress management and regular exercise could help significantly.",
        },
        "spiritual": {
            "strong": "Spiritual authority and self-realization themes may be prominent. Connection with one's dharma and life purpose could deepen.",
            "moderate": "Spiritual growth through discipline and self-reflection is indicated. Finding one's authentic path may be a gradual process.",
            "weak": "Spiritual growth may come through ego dissolution and surrender. Challenges could serve as catalysts for deeper self-understanding.",
        },
    },
    "Moon": {
        "career": {
            "strong": "Career may flourish in nurturing, public-facing, or creative fields. Emotional intelligence could be a significant professional asset.",
            "moderate": "Career tends to involve steady work in service or public-related roles. Emotional fluctuations may occasionally affect professional focus.",
            "weak": "Career may experience instability or frequent changes. Finding emotional grounding and a supportive work environment is important.",
        },
        "relationships": {
            "strong": "Deep emotional bonds and nurturing relationships are supported. Family life and domestic harmony may bring great satisfaction.",
            "moderate": "Relationships are emotionally engaged but may experience periodic fluctuations. Patience with emotional changes strengthens bonds.",
            "weak": "Emotional sensitivity may create relationship challenges. Learning healthy emotional expression and boundary-setting could help significantly.",
        },
        "finance": {
            "strong": "Wealth may come through public dealings, real estate, or nurturing professions. Financial security through savings and property is indicated.",
            "moderate": "Finances tend to fluctuate with emotional and domestic needs. Building savings and avoiding impulsive spending is advisable.",
            "weak": "Financial instability may be linked to emotional decision-making. Creating structured budgets and avoiding impulsive purchases is recommended.",
        },
        "health": {
            "strong": "Emotional and physical health tend to be well-balanced. Good mental peace and contentment support overall wellness.",
            "moderate": "Health is generally stable but mental wellness needs attention. Stress, anxiety, and sleep patterns may require management.",
            "weak": "Mental health and emotional wellness may need significant attention. Water retention, cold-related issues, and sleep disturbances are possible.",
        },
        "spiritual": {
            "strong": "Deep spiritual insights through meditation and emotional awareness are supported. Connection with the divine feminine may deepen.",
            "moderate": "Spiritual growth through devotion, compassion, and emotional surrender is indicated. Pilgrimage and sacred spaces may bring peace.",
            "weak": "Spiritual growth may come through emotional upheaval and the need to find inner peace. Meditation and counseling could be beneficial.",
        },
    },
    "Mars": {
        "career": {
            "strong": "Career may thrive in engineering, military, sports, or action-oriented fields. Courage and initiative could lead to significant achievements.",
            "moderate": "Career progress through determined effort and competitive drive. Technical and physical skills may be your strongest assets.",
            "weak": "Career may face conflicts, accidents, or aggressive competition. Channeling energy constructively and avoiding impulsive decisions is crucial.",
        },
        "relationships": {
            "strong": "Passionate and protective relationships are indicated. Courage to defend loved ones and take initiative in love matters.",
            "moderate": "Relationships are energetic but may need patience to avoid conflicts. Physical chemistry and shared activities strengthen bonds.",
            "weak": "Relationship conflicts, arguments, or separation themes may arise. Anger management and choosing battles wisely is essential.",
        },
        "finance": {
            "strong": "Financial gains through property, engineering, military service, or competitive ventures. Land and real estate investments may prosper.",
            "moderate": "Finances are driven by hard work and physical effort. Income from technical skills and property-related activities.",
            "weak": "Financial losses through conflicts, legal disputes, or hasty decisions are possible. Avoiding aggressive financial strategies is advisable.",
        },
        "health": {
            "strong": "Physical strength, energy, and athletic ability tend to be at their peak. Robust constitution supports an active lifestyle.",
            "moderate": "Health is generally strong but attention to injuries, inflammation, and blood-related issues is advisable.",
            "weak": "Health challenges related to accidents, surgeries, or inflammatory conditions may arise. Extra caution in physical activities is recommended.",
        },
        "spiritual": {
            "strong": "Spiritual warrior energy — courage to face inner demons and transform negativity. Kundalini practices may be beneficial.",
            "moderate": "Spiritual growth through discipline, physical practices (yoga, martial arts), and service to others.",
            "weak": "Spiritual growth through overcoming anger and aggression. Learning non-violence and patience could be transformative.",
        },
    },
    "Mercury": {
        "career": {
            "strong": "Career may excel in business, communication, education, IT, or analytical fields. Intellectual abilities could bring significant professional success.",
            "moderate": "Career involves steady intellectual work and communication roles. Networking and continuous learning support advancement.",
            "weak": "Career may face challenges in communication, decision-making, or nervous tension. Developing focus and reducing scattered energy helps.",
        },
        "relationships": {
            "strong": "Relationships thrive through intellectual connection and communication. Witty, engaging partnerships with shared learning interests.",
            "moderate": "Relationships benefit from open communication and intellectual sharing. May sometimes overthink emotional matters.",
            "weak": "Communication breakdowns in relationships are possible. Nervous tension and indecisiveness may create misunderstandings.",
        },
        "finance": {
            "strong": "Financial success through business, trade, writing, education, or technology. Multiple income streams and clever investments indicated.",
            "moderate": "Finances are managed through analytical skills and diverse activities. Consistent effort in business or employment brings stability.",
            "weak": "Financial challenges through poor decisions, fraud, or scattered investments. Seeking professional financial advice is strongly recommended.",
        },
        "health": {
            "strong": "Mental agility and nervous system health tend to be excellent. Quick recovery from illnesses is indicated.",
            "moderate": "Health is generally good but nervous system, skin, and respiratory issues may need attention.",
            "weak": "Nervous disorders, skin issues, and mental stress may be prominent. Meditation, adequate sleep, and stress reduction are essential.",
        },
        "spiritual": {
            "strong": "Spiritual growth through knowledge, scripture study, and intellectual understanding of divine principles.",
            "moderate": "Spiritual development through learning, mantra practice, and intellectual inquiry into life's deeper questions.",
            "weak": "Spiritual growth may be hindered by overthinking. Learning to quiet the mind through meditation could be transformative.",
        },
    },
    "Jupiter": {
        "career": {
            "strong": "Career may flourish in education, law, finance, spirituality, or advisory roles. Wisdom and ethics could bring significant recognition and growth.",
            "moderate": "Career progress through teaching, counseling, or advisory capacities. Ethical conduct and continuous learning support advancement.",
            "weak": "Career growth may be slow or face challenges in credibility. Overexpansion and overcommitment should be avoided.",
        },
        "relationships": {
            "strong": "Relationships are blessed with wisdom, generosity, and mutual growth. Marriage and family life may bring deep fulfillment.",
            "moderate": "Relationships benefit from shared values and spiritual compatibility. Children and family matters may be in focus.",
            "weak": "Relationship challenges may arise from overindulgence, lack of boundaries, or differing values. Maintaining realistic expectations helps.",
        },
        "finance": {
            "strong": "Significant wealth accumulation through wise investments, inheritance, or fortunate opportunities. Financial expansion and prosperity indicated.",
            "moderate": "Finances grow steadily through ethical means and wise management. Conservative investments in education or property are favored.",
            "weak": "Financial overextension or poor judgment in investments may cause setbacks. Avoid lending large amounts and speculative ventures.",
        },
        "health": {
            "strong": "Overall health tends to be excellent with natural resilience and vitality. Good liver function and robust constitution.",
            "moderate": "Health is generally good but attention to liver, weight management, and diabetes prevention is advisable.",
            "weak": "Health may be affected by weight gain, liver issues, or cholesterol. Dietary discipline and regular exercise are important.",
        },
        "spiritual": {
            "strong": "Profound spiritual growth, wisdom, and possibly a role as a spiritual guide or teacher. Connection with higher knowledge deepens.",
            "moderate": "Spiritual development through study of scriptures, pilgrimages, and association with wise mentors.",
            "weak": "Spiritual growth may be slow but steady. Overcoming materialistic tendencies and cultivating genuine devotion helps.",
        },
    },
    "Venus": {
        "career": {
            "strong": "Career may flourish in arts, entertainment, luxury, beauty, or diplomacy. Creative talents could bring both recognition and wealth.",
            "moderate": "Career involves creative, aesthetic, or relationship-oriented work. Comfort and pleasant working conditions are important.",
            "weak": "Career may face challenges due to overindulgence or lack of discipline. Balancing pleasure with professional responsibilities is key.",
        },
        "relationships": {
            "strong": "This is one of the most favorable periods for love, marriage, and romantic fulfillment. Deep harmony and mutual attraction in partnerships.",
            "moderate": "Relationships bring comfort and companionship. Romance is present but may require effort to maintain excitement and connection.",
            "weak": "Relationship challenges through excessive attachment, jealousy, or unfulfilled desires. Learning unconditional love and acceptance helps.",
        },
        "finance": {
            "strong": "Wealth through luxury goods, arts, entertainment, or partnerships. Material comforts and financial abundance indicated.",
            "moderate": "Finances support a comfortable lifestyle. Income through creative work, partnerships, or beauty-related industries.",
            "weak": "Financial challenges through overspending on luxuries or pleasures. Creating budgets and exercising financial discipline is essential.",
        },
        "health": {
            "strong": "Health tends to be good with focus on beauty, skin health, and reproductive wellness. A comfortable and pleasant lifestyle supports well-being.",
            "moderate": "Health is generally stable but reproductive system, kidneys, and skin may need periodic attention.",
            "weak": "Health challenges related to reproductive system, kidneys, or diabetes may arise. Avoiding excessive sugar and luxury foods helps.",
        },
        "spiritual": {
            "strong": "Spiritual growth through beauty, devotion (bhakti), and aesthetic appreciation of the divine. Temple arts and sacred music may inspire.",
            "moderate": "Spiritual development through devotional practices, gratitude, and appreciating beauty in all forms.",
            "weak": "Spiritual growth may be challenged by attachment to material pleasures. Learning to see beauty as divine expression helps.",
        },
    },
    "Saturn": {
        "career": {
            "strong": "Career success through discipline, hard work, and long-term perseverance. Authority positions earned through sustained effort and integrity.",
            "moderate": "Career progress is slow but steady. Hard work, patience, and persistence eventually bring recognition and stability.",
            "weak": "Career faces significant challenges, delays, and obstacles. This is a period of karmic lessons where persistence and humility are essential.",
        },
        "relationships": {
            "strong": "Relationships may be with mature, responsible, and loyal partners. Long-lasting commitments and dutiful partnerships.",
            "moderate": "Relationships involve responsibility and duty. Age differences with partners are possible. Loyalty and commitment are valued.",
            "weak": "Relationship challenges through coldness, distance, or heavy responsibilities. Loneliness may be a theme requiring inner strength.",
        },
        "finance": {
            "strong": "Wealth through discipline, savings, and long-term investments. Property, agriculture, and mining-related income possible.",
            "moderate": "Finances require careful management and disciplined saving. Slow but steady wealth accumulation through hard work.",
            "weak": "Financial hardship, debts, or losses are possible. This period demands extreme financial discipline and conservative management.",
        },
        "health": {
            "strong": "Constitution is enduring though not necessarily vibrant. Longevity and resilience through disciplined health practices.",
            "moderate": "Health requires attention to joints, bones, teeth, and chronic conditions. Regular exercise and adequate rest are important.",
            "weak": "Health challenges related to bones, joints, chronic diseases, or depression may arise. Professional medical care and mental health support are strongly advised.",
        },
        "spiritual": {
            "strong": "Deep spiritual maturity through discipline, meditation, and service. Karmic debts may be resolved through dedicated practice.",
            "moderate": "Spiritual growth through patience, service to the less fortunate, and acceptance of life's challenges as lessons.",
            "weak": "This period may feel spiritually dark but contains seeds of profound transformation. Surrender and acceptance are the greatest teachers.",
        },
    },
    "Rahu": {
        "career": {
            "strong": "Career may skyrocket through unconventional means, technology, foreign connections, or innovative ventures. Sudden rise in status possible.",
            "moderate": "Career involves non-traditional paths, technology, or foreign influences. Ambition drives progress but focus is needed.",
            "weak": "Career may face confusion, deception, or sudden reversals. Avoiding shortcuts and unethical means is crucial for long-term stability.",
        },
        "relationships": {
            "strong": "Relationships may involve foreign, intercultural, or unconventional partnerships. Exciting and transformative connections.",
            "moderate": "Relationships involve non-traditional dynamics or cross-cultural elements. Open-mindedness and adaptability strengthen bonds.",
            "weak": "Relationship confusion, deception, or obsessive attachments may arise. Maintaining clarity and healthy boundaries is essential.",
        },
        "finance": {
            "strong": "Sudden financial gains through technology, foreign trade, speculation, or unexpected windfalls. Material ambitions may be fulfilled.",
            "moderate": "Finances driven by worldly ambition and material desire. Technology and innovation-related income possible.",
            "weak": "Financial losses through fraud, deception, or risky speculation. Extreme caution with investments and business partnerships is advised.",
        },
        "health": {
            "strong": "Health is generally maintained but may involve unusual or hard-to-diagnose conditions. Alternative medicine may be helpful.",
            "moderate": "Health needs attention to mysterious ailments, allergies, or psychological issues. Multiple medical opinions may be beneficial.",
            "weak": "Health challenges from toxins, addictions, or mysterious illnesses. Strict lifestyle discipline and avoiding intoxicants is critical.",
        },
        "spiritual": {
            "strong": "Spiritual breakthroughs through unconventional paths, tantric practices, or foreign spiritual traditions. Deep occult understanding.",
            "moderate": "Spiritual curiosity and exploration of diverse traditions. Past-life themes and karmic patterns may surface for resolution.",
            "weak": "Spiritual confusion or attraction to negative practices. Grounding in traditional wisdom and finding a trustworthy guide is important.",
        },
    },
    "Ketu": {
        "career": {
            "strong": "Career may involve spiritual, healing, or research-oriented work. Detachment from worldly ambition may paradoxically bring recognition.",
            "moderate": "Career involves introspective or behind-the-scenes work. Research, investigation, and spiritual vocations are indicated.",
            "weak": "Career confusion, lack of direction, or sudden disruptions. Learning to trust the process and surrender outcomes helps.",
        },
        "relationships": {
            "strong": "Relationships may have a spiritual or karmic quality. Deep soul connections and past-life bonds may be experienced.",
            "moderate": "Relationships involve spiritual growth and sometimes detachment. Learning to balance worldly and spiritual needs in partnerships.",
            "weak": "Relationship losses, separations, or emotional detachment may occur. This period teaches the value of non-attachment and self-reliance.",
        },
        "finance": {
            "strong": "Financial gains through spiritual work, healing, or inherited assets. Detachment from money paradoxically attracts it.",
            "moderate": "Finances are adequate but not the primary focus. Income from spiritual, research, or healing activities.",
            "weak": "Financial losses or lack of interest in material pursuits. Basic needs are met but accumulation is not supported.",
        },
        "health": {
            "strong": "Health benefits from spiritual practices, meditation, and alternative healing. Psychic sensitivity may be heightened.",
            "moderate": "Health requires attention to mysterious or difficult-to-diagnose conditions. Spiritual healing practices may complement medical care.",
            "weak": "Health challenges from unclear causes, psychosomatic issues, or accidents. Grounding practices and regular medical attention are important.",
        },
        "spiritual": {
            "strong": "Profound spiritual awakening and liberation (moksha) themes. This may be one of the most spiritually significant periods of life.",
            "moderate": "Spiritual growth through meditation, detachment, and inner exploration. Past-life wisdom may surface and guide decisions.",
            "weak": "Spiritual disorientation or loss of faith. This dark night of the soul ultimately leads to deeper understanding when navigated with patience.",
        },
    },
}

# Transit effect descriptions for decade predictions
TRANSIT_EFFECTS = {
    "jupiter_kendra": "Jupiter transiting a kendra (angular) house from your Lagna supports career growth and overall prosperity during this period.",
    "jupiter_trikona": "Jupiter in a trikona (trinal) house brings wisdom, fortune, and spiritual expansion to this phase of life.",
    "jupiter_trik": "Jupiter in a challenging house may limit expansion but encourages inner growth and philosophical understanding.",
    "saturn_sade_sati": "Sade Sati (Saturn near your Moon) is active during this period. This may bring significant life lessons, restructuring, and ultimately, inner strength through challenges.",
    "saturn_ashtama": "Ashtama Shani (Saturn in 8th from Moon) may bring transformative experiences and hidden challenges that build long-term resilience.",
    "saturn_kendra": "Saturn in a kendra house brings discipline, structure, and long-term career building during this phase.",
    "rahu_lagna": "Rahu influencing your ascendant area may bring unconventional experiences, worldly ambitions, and a desire for transformation.",
    "rahu_seventh": "Rahu near the 7th house axis suggests unconventional relationship dynamics or foreign connections during this period.",
    "ketu_spiritual": "Ketu's influence on spiritual houses may deepen meditation practice, detachment, and inner transformation.",
    "ketu_lagna": "Ketu near the ascendant area may bring introspection, spiritual seeking, and a period of self-discovery.",
}

ENGINE_VERSION = "vedic-rule-engine-v1.0.0"
