// src/types/categories.types.ts
// Represents a financial category (expenses, revenues, or wallets)

export interface CategoryData {
  color: string;
  editable: boolean;
  icon: string;
  id: number;
  name: string;
  subcategories?: Subcategory[];
  type: "despesa" | "receita" | "ambas" | "contas";
}

// Represents a subcategory within a category
export interface Subcategory {
  color: string;
  editable: boolean;
  icon: string;
  id: number;
  name: string;
  type: "despesa" | "receita" | "ambas";
}