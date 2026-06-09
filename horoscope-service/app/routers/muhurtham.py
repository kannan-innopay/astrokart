"""Muhurtham (auspicious timing) API router."""

from fastapi import APIRouter, HTTPException
from pydantic import BaseModel

from app.services.muhurtham_calculator import MUHURTHAM_RULES, find_auspicious_dates

router = APIRouter(prefix="/api/muhurtham", tags=["muhurtham"])


class MuhurthamRequest(BaseModel):
    date_start: str  # YYYY-MM-DD
    date_end: str  # YYYY-MM-DD
    purpose: str
    user_chart: dict | None = None  # Full birth chart for personalized scoring


class MuhurthamDate(BaseModel):
    date: str
    weekday: str
    score: int
    tithi: str
    nakshatra: str
    nakshatra_index: int
    rashi: str
    rashi_index: int
    yoga: str
    reasons: list[str]
    personalized: bool = False
    transit_highlights: list[dict] | None = None


class MuhurthamResponse(BaseModel):
    purpose: str
    dates: list[MuhurthamDate]
    available_purposes: list[str]


@router.post("/search", response_model=MuhurthamResponse)
async def search_muhurtham(request: MuhurthamRequest):
    """Find auspicious dates for a given purpose within a date range."""
    try:
        dates = find_auspicious_dates(
            date_start=request.date_start,
            date_end=request.date_end,
            purpose=request.purpose,
            user_chart=request.user_chart,
        )

        return MuhurthamResponse(
            purpose=request.purpose,
            dates=dates,
            available_purposes=list(MUHURTHAM_RULES.keys()),
        )
    except Exception as e:
        raise HTTPException(status_code=500, detail=f"Muhurtham search failed: {str(e)}")


@router.get("/purposes")
async def list_purposes():
    """List available muhurtham purposes."""
    return {"purposes": list(MUHURTHAM_RULES.keys())}
