from fastapi import FastAPI

from app.routers.analysis import router as analysis_router
from app.routers.chart import router as chart_router
from app.routers.compatibility import router as compatibility_router
from app.routers.muhurtham import router as muhurtham_router
from app.routers.predictions import router as predictions_router
from app.routers.transit import router as transit_router

app = FastAPI(
    title="Astrokart Horoscope Service",
    description="Vedic astrology birth chart and transit calculation API",
    version="1.1.0",
)

app.include_router(chart_router)
app.include_router(transit_router)
app.include_router(analysis_router)
app.include_router(muhurtham_router)
app.include_router(predictions_router)
app.include_router(compatibility_router)


@app.get("/")
def root():
    return {"service": "astrokart-horoscope", "version": "1.0.0"}
