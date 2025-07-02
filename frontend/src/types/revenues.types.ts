import { MonthData } from ".";

export interface Revenues {
    valuePay: number;
    valuePending: number;
    valueTotalMonth: number;
    byMonth: MonthData[];
    totalDay: number;
    // byCategory: CategoryData[];
}