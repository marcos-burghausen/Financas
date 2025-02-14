from django.http import JsonResponse # type:ignore
from django.middleware.csrf import get_token # type:ignore

def get_csrf_token(request):
    csrf_token = get_token(request)
    return JsonResponse({'csrfToken': csrf_token})
