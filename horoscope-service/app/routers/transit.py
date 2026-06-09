from fastapi import APIRouter, HTTPException

from app.models.transit_models import (
    CurrentTransitsResponse,
    ForecastRequest,
    ForecastResponse,
    UpcomingTransitsResponse,
)
from app.services.transit_calculator import (
    get_current_positions,
    get_forecast,
    get_upcoming_transits,
)

router = APIRouter(prefix="/api/transit", tags=["transit"])


@router.get("/current", response_model=CurrentTransitsResponse)
def current_transits():
    try:
        return get_current_positions()
    except Exception as e:
        raise HTTPException(status_code=500, detail=str(e))


@router.get("/upcoming", response_model=UpcomingTransitsResponse)
def upcoming_transits(months: int = 12):
    try:
        events = get_upcoming_transits(months=min(months, 24))
        return {"events": events}
    except Exception as e:
        raise HTTPException(status_code=500, detail=str(e))


@router.post("/forecast", response_model=ForecastResponse)
def forecast(request: ForecastRequest):
    try:
        return get_forecast(request.moon_rashi_index, request.lagna_rashi_index)
    except Exception as e:
        raise HTTPException(status_code=500, detail=str(e))
