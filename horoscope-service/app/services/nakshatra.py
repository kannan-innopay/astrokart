RASHIS = [
    {"name": "Aries", "sanskrit": "Mesha"},
    {"name": "Taurus", "sanskrit": "Vrishabha"},
    {"name": "Gemini", "sanskrit": "Mithuna"},
    {"name": "Cancer", "sanskrit": "Karka"},
    {"name": "Leo", "sanskrit": "Simha"},
    {"name": "Virgo", "sanskrit": "Kanya"},
    {"name": "Libra", "sanskrit": "Tula"},
    {"name": "Scorpio", "sanskrit": "Vrishchika"},
    {"name": "Sagittarius", "sanskrit": "Dhanu"},
    {"name": "Capricorn", "sanskrit": "Makara"},
    {"name": "Aquarius", "sanskrit": "Kumbha"},
    {"name": "Pisces", "sanskrit": "Meena"},
]

NAKSHATRAS = [
    "Ashwini", "Bharani", "Krittika", "Rohini", "Mrigashira", "Ardra",
    "Punarvasu", "Pushya", "Ashlesha", "Magha", "Purva Phalguni",
    "Uttara Phalguni", "Hasta", "Chitra", "Swati", "Vishakha",
    "Anuradha", "Jyeshtha", "Mula", "Purva Ashadha", "Uttara Ashadha",
    "Shravana", "Dhanishta", "Shatabhisha", "Purva Bhadrapada",
    "Uttara Bhadrapada", "Revati",
]

GRAHA_SANSKRIT = {
    "Sun": "Surya",
    "Moon": "Chandra",
    "Mars": "Mangal",
    "Mercury": "Budha",
    "Jupiter": "Guru",
    "Venus": "Shukra",
    "Saturn": "Shani",
    "Rahu": "Rahu",
    "Ketu": "Ketu",
}

NAKSHATRA_SPAN = 13 + 1 / 3  # 13°20'
PADA_SPAN = NAKSHATRA_SPAN / 4  # 3°20'


def get_rashi(longitude: float) -> dict:
    """Get rashi info from sidereal longitude (0-360)."""
    index = int(longitude / 30) % 12
    return {"index": index, "name": RASHIS[index]["name"], "sanskrit": RASHIS[index]["sanskrit"]}


def get_nakshatra(longitude: float) -> dict:
    """Get nakshatra and pada from sidereal longitude (0-360)."""
    nak_index = int(longitude / NAKSHATRA_SPAN) % 27
    pada = int((longitude % NAKSHATRA_SPAN) / PADA_SPAN) + 1
    if pada > 4:
        pada = 4
    return {"index": nak_index, "name": NAKSHATRAS[nak_index], "pada": pada}


def get_graha_sanskrit(name: str) -> str:
    """Get Sanskrit name for a graha."""
    return GRAHA_SANSKRIT.get(name, name)
