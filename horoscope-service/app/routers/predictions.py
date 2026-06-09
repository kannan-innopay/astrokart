"""Daily and monthly prediction API router."""

from fastapi import APIRouter, HTTPException
from pydantic import BaseModel

from app.services.prediction_engine import generate_daily_prediction, generate_monthly_forecast

router = APIRouter(prefix="/api/predictions", tags=["predictions"])


class DailyPredictionRequest(BaseModel):
    chart_data: dict
    date_of_birth: str
    target_date: str


class MonthlyForecastRequest(BaseModel):
    chart_data: dict
    date_of_birth: str
    year: int
    month: int


@router.post("/daily")
async def daily_prediction(request: DailyPredictionRequest):
    """Generate a personalized daily prediction."""
    try:
        return generate_daily_prediction(
            chart_data=request.chart_data,
            date_of_birth=request.date_of_birth,
            target_date=request.target_date,
        )
    except Exception as e:
        raise HTTPException(status_code=500, detail=f"Prediction failed: {str(e)}")


@router.post("/monthly")
async def monthly_forecast(request: MonthlyForecastRequest):
    """Generate a monthly forecast with week-by-week breakdown."""
    try:
        return generate_monthly_forecast(
            chart_data=request.chart_data,
            date_of_birth=request.date_of_birth,
            year=request.year,
            month=request.month,
        )
    except Exception as e:
        raise HTTPException(status_code=500, detail=f"Forecast failed: {str(e)}")
