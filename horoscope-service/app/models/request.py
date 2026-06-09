from pydantic import BaseModel, Field


class ChartRequest(BaseModel):
    date_of_birth: str = Field(description="Date of birth in YYYY-MM-DD format")
    time_of_birth: str = Field(default="12:00", description="Time of birth in HH:MM format (24h)")
    latitude: float = Field(description="Birth place latitude")
    longitude: float = Field(description="Birth place longitude")
    timezone_offset: float = Field(default=5.5, description="Timezone offset from UTC in hours")
    name: str = Field(default="", description="Person's name")
    place_of_birth: str = Field(default="", description="Birth place name")
