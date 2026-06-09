from pydantic import BaseModel


class RashiInfo(BaseModel):
    index: int
    name: str
    sanskrit: str


class NakshatraInfo(BaseModel):
    index: int
    name: str
    pada: int


class GrahaInfo(BaseModel):
    name: str
    sanskrit: str
    longitude: float
    is_retrograde: bool
    rashi: RashiInfo
    nakshatra: NakshatraInfo
    house: int


class HouseInfo(BaseModel):
    number: int
    sign_index: int
    sign: str
    sign_sanskrit: str


class LagnaInfo(BaseModel):
    longitude: float
    rashi: RashiInfo
    nakshatra: NakshatraInfo


class PanchangaInfo(BaseModel):
    tithi: str
    nakshatra: str
    yoga: str
    karana: str
    vaara: str


class ChartResponse(BaseModel):
    ayanamsa: float
    lagna: LagnaInfo
    grahas: list[GrahaInfo]
    houses: list[HouseInfo]
    panchanga: PanchangaInfo
