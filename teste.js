let despesas = [
    { id: 7, user_id: 1, valor: 1000, date: "2023-10-04", descricao: "gsdgvsd", categoria: "Salario" },
    { id: 9, user_id: 1, valor: 1000, date: "2023-10-05", descricao: "kdfj", categoria: "Salario" },
    { id: 10, user_id: 1, valor: 1000, date: "2023-01-10", descricao: "Pagamento", categoria: "Salario" },
    { id: 11, user_id: 1, valor: 1000, date: "2023-02-10", descricao: "Pagamento", categoria: "Salario" },
    { id: 12, user_id: 1, valor: 1000, date: "2023-03-10", descricao: "Pagamento", categoria: "Salario" },
    { id: 13, user_id: 1, valor: 1000, date: "2023-04-10", descricao: "Pagamento", categoria: "Salario" },
    { id: 14, user_id: 1, valor: 1000, date: "2023-05-10", descricao: "Pagamento", categoria: "Salario" },
    // { id: 15, user_id: 1, valor: 1000, date: "2023-06-10", descricao: "Pagamento", categoria: "Salario" },
    { id: 16, user_id: 1, valor: 1000, date: "2023-07-10", descricao: "Pagamento", categoria: "Salario" },
    { id: 17, user_id: 1, valor: 1000, date: "2023-08-10", descricao: "Pagamento", categoria: "Salario" },
    { id: 18, user_id: 1, valor: 1000, date: "2023-09-10", descricao: "Pagamento", categoria: "Salario" },
    { id: 19, user_id: 1, valor: 2000, date: "2023-01-20", descricao: "Vale", categoria: "Salario" },
    { id: 20, user_id: 1, valor: 1000, date: "2023-02-20", descricao: "Vale", categoria: "Salario" },
    { id: 21, user_id: 1, valor: 1000, date: "2023-03-20", descricao: "Vale", categoria: "Salario" },
    { id: 22, user_id: 1, valor: 1000, date: "2023-04-20", descricao: "Vale", categoria: "Salario" }
]

// Função para agrupar despesas por mês
function groupByMonth(expenses) {
    const grouped = {};

    expenses.forEach((expense) => {
        const data = new Date(expense.date);
        const mesAno = `${data.getMonth() + 1}/${data.getFullYear()}`;

        if (!grouped[mesAno]) {
            grouped[mesAno] = [];
        }

        grouped[mesAno].push(expense);
    });

    return grouped;
}

// Função para calcular o valor total de cada mês
function calcularTotalPorMes(expenses) {
    const grouped = groupByMonth(expenses);
    const totalPorMes = {};

    for (const mesAno in grouped) {
        if (grouped.hasOwnProperty(mesAno)) {
            const despesasNoMes = grouped[mesAno];
            const total = despesasNoMes.reduce((acc, expense) => acc + expense.valor, 0);
            totalPorMes[mesAno] = total;
        }
    }

    return totalPorMes;
}

// Calcular o valor total de cada mês
const totalPorMes = calcularTotalPorMes(despesas);

console.log(totalPorMes);




const data1 = new Date("2023-09-05");
const options = { month: 'long' };
const mesString = data1.toLocaleString('pt-BR', options);
// console.log(mesString);


const despesasPorMes = {};

despesas.forEach(despesa => {
    const data = new Date(despesa.date);
    const options = { month: 'long' };
    const mesAno = data.toLocaleString('pt-BR', options);
    // const mesAno = data.getMonth() + 1;
    if (!despesasPorMes[mesAno]) {
        despesasPorMes[mesAno] = [];
    }

    despesasPorMes[mesAno].push(despesa);

});
despesasPorMes;
// console.log(despesasPorMes);

