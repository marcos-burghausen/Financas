import { CategoryData, MonthData } from "./";

export interface Expenses {
    valuePay: number;
    valuePending: number;
    valueTotalMonth: number;
    byMonth: MonthData[];
    totalDay: number;
    byCategory: CategoryData[];
}