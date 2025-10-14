export const formatValue = (value: string): string =>{
    if (isNaN(Number(value))) {
        return "0,00";
    }
    const valueFormatted = (Number(value) / 100).toLocaleString("pt-BR", { style: "decimal", minimumFractionDigits: 2, maximumFractionDigits: 2 });
    return valueFormatted;
};
