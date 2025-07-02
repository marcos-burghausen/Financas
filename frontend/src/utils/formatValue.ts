export const formatValue = (value: number): string =>{
    if (isNaN(value)) {
        return "0,00";
    }
    const valueFormatted = (value / 100).toLocaleString("pt-BR", { style: "decimal", minimumFractionDigits: 2, maximumFractionDigits: 2 });
    return valueFormatted;
};
