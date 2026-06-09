"""Chart analysis API router."""

from fastapi import APIRouter, HTTPException

from app.models.analysis_models import AnalysisRequest, AnalysisResponse
from app.services.analysis_engine import generate_free_summary, generate_full_analysis

router = APIRouter(prefix="/api/chart", tags=["analysis"])


@router.post("/analyze", response_model=AnalysisResponse)
async def analyze_chart(request: AnalysisRequest):
    """Generate a full Vedic astrology analysis from birth chart data."""
    try:
        full_analysis = generate_full_analysis(request.chart_data, request.date_of_birth)
        free_summary = generate_free_summary(full_analysis)

        return AnalysisResponse(
            personality=full_analysis["personality"],
            career=full_analysis["career"],
            marriage=full_analysis["marriage"],
            finance=full_analysis["finance"],
            wellness=full_analysis["wellness"],
            dasha=full_analysis["dasha"],
            decades=full_analysis.get("decades", []),
            free_summary=free_summary,
            engine_version=full_analysis["engine_version"],
            disclaimer=full_analysis["disclaimer"],
        )
    except Exception as e:
        raise HTTPException(status_code=500, detail=f"Analysis failed: {str(e)}")
