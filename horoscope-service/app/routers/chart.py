from fastapi import APIRouter, HTTPException

from app.models.request import ChartRequest
from app.models.response import ChartResponse
from app.services.chart_calculator import generate_chart

router = APIRouter(prefix="/api/chart", tags=["chart"])


@router.get("/health")
def health():
    return {"status": "ok", "service": "horoscope"}


@router.post("/generate", response_model=ChartResponse)
def generate(request: ChartRequest):
    try:
        return generate_chart(request)
    except Exception as e:
        raise HTTPException(status_code=500, detail=str(e))
