from django.urls import path #type: ignore
from . import views

urlpatterns = [
    path('csrf-token/', views.get_csrf_token, name='csrf-token'),
]