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
export type ErrorCodes = string; 

export interface ApiErrorResponse {
  errors?: Record<string, string[]>;
  message?: string;
}