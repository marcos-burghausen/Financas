export const formatValue = (value: number): string =>{
    const valueFormatted = (value / 100).toLocaleString("pt-BR", { style: "decimal", minimumFractionDigits: 2, maximumFractionDigits: 2 });
    return valueFormatted;
};
