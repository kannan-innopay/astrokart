from pydantic import BaseModel


class TransitGraha(BaseModel):
    name: str
    rashi_index: int
    rashi_name: str
    longitude: float
    nakshatra: str
    nakshatra_index: int
    is_retrograde: bool


class CurrentTransitsResponse(BaseModel):
    date: str
    grahas: list[TransitGraha]


class TransitEvent(BaseModel):
    planet: str
    from_rashi_index: int
    from_rashi: str
    to_rashi_index: int
    to_rashi: str
    date: str
    significance: str | None = None


class UpcomingTransitsResponse(BaseModel):
    events: list[TransitEvent]


class ForecastRequest(BaseModel):
    moon_rashi_index: int
    lagna_rashi_index: int


class TransitHouse(BaseModel):
    planet: str
    rashi_index: int
    house_from_moon: int
    house_from_lagna: int
    is_retrograde: bool


class SadeSatiStatus(BaseModel):
    active: bool
    phase: str | None = None


class ForecastResponse(BaseModel):
    transits: list[TransitHouse]
    sade_sati: SadeSatiStatus
    ashtama_shani: bool
