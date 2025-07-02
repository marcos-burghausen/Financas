// src/types/shared.types.ts
// Represents a color value
export interface Colors {
  color: string;
}

// Represents an icon item
export interface IconItem {
  icon: string;
}

// Represents error codes from errorCodes.json
export type ErrorCodes = keyof typeof import("@/assets/errorCodes.json"); 

export interface ApiErrorResponse {
  errors?: { [key: string]: string | string[] };
  error_code?: ErrorCodes;
  message?: string;
}