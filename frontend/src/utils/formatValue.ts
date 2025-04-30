export const formatValue = (value: string): string =>{
    const valueFormatted = (value / 100).toLocaleString("pt-BR", { style: "decimal", minimumFractionDigits: 2, maximumFractionDigits: 2 });
    return valueFormatted;
};
