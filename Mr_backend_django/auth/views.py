from django.shortcuts import render # type: ignore
from django.http import JsonResponse # type: ignore
from django.views.decorators.csrf import csrf_exempt # type: ignore

@csrf_exempt
def login(request):
    # lógica de login
    return JsonResponse({'message': 'Login successful', 'request': request})

def logout(request):
    # lógica de logout
    return JsonResponse({'message': 'Logout successful'})

def register(request):
    # lógica de registro
    return JsonResponse({'message': 'Registration successful'})
