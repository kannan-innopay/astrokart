"""Compatibility matching API router."""

from fastapi import APIRouter, HTTPException
from pydantic import BaseModel

from app.services.compatibility_engine import calculate_compatibility

router = APIRouter(prefix="/api/compatibility", tags=["compatibility"])


class CompatibilityRequest(BaseModel):
    person1_moon_nakshatra: int  # 0-26
    person1_moon_rashi: int  # 0-11
    person2_moon_nakshatra: int  # 0-26
    person2_moon_rashi: int  # 0-11


@router.post("/match")
async def match_compatibility(request: CompatibilityRequest):
    """Calculate Ashtakoota compatibility score."""
    try:
        return calculate_compatibility(
            person1_moon_nakshatra=request.person1_moon_nakshatra,
            person1_moon_rashi=request.person1_moon_rashi,
            person2_moon_nakshatra=request.person2_moon_nakshatra,
            person2_moon_rashi=request.person2_moon_rashi,
        )
    except Exception as e:
        raise HTTPException(status_code=500, detail=f"Compatibility check failed: {str(e)}")
