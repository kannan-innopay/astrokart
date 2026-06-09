"""Pydantic models for chart analysis API."""

from pydantic import BaseModel


class AnalysisRequest(BaseModel):
    chart_data: dict
    date_of_birth: str  # YYYY-MM-DD


class PersonalitySummary(BaseModel):
    lagna_rashi: str
    moon_sign: str
    dominant_element: str
    summary: str
    lagna_traits: str


class FreeSummarySection(BaseModel):
    summary: str


class FreeDashaSummary(BaseModel):
    current_lord: str | None = None
    summary: str


class FreeSummary(BaseModel):
    personality: PersonalitySummary
    career: FreeSummarySection
    marriage: dict
    finance: dict
    wellness: dict
    dasha: FreeDashaSummary
    engine_version: str
    disclaimer: str


class AnalysisResponse(BaseModel):
    """Full analysis response — contains all sections with complete detail."""
    personality: dict
    career: dict
    marriage: dict
    finance: dict
    wellness: dict
    dasha: dict
    decades: list[dict] = []
    free_summary: dict
    engine_version: str
    disclaimer: str
