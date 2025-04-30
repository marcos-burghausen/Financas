// src/types/categories.types.ts
// Represents a financial category (expenses, revenues, or wallets)

export interface Category {
    id: number;
    name: string;
    color: string;
    icon: string;
    editable: boolean;
    type: "despesa" | "receita" | "ambas" | "contas";
    subcategories?: Subcategory[];
  }
  
  // Represents a subcategory within a category
  export interface Subcategory {
    id: number;
    name: string;
    color: string;
    icon: string;
    editable: boolean;
    type: "despesa" | "receita" | "ambas";
  }