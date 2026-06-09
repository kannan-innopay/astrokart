<?php

namespace App\Enums;

enum ConsultationMode: string
{
    case Chat = 'chat';
    case Call = 'call';
    case VideoCall = 'video_call';
}
