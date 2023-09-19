<template>
    <div class="box">
        <div class="container__dados">
            <h2 class="title">faça login no Mr Finanças</h2>
            <div class="social__media">
                <ul class="list__social__media">
                    <a class="link__social__media" href="#">
                        <li class="item__social__media">
                            <mdicon class="icon__modify" name="facebook" />
                        </li>
                    </a>
                    <a class="link__social__media" href="#">
                        <li class="item__social__media">
                            <mdicon class="icon__modify" name="google" />
                        </li>
                    </a>
                    <a class="link__social__media" href="#">
                        <li class="item__social__media">
                            <mdicon class="icon__modify" name="linkedin" />
                        </li>
                    </a>
                </ul>
            </div>
            <p class="sub__title">ou use sua conta de e-mail:</p>
            <form class="form" @submit.prevent="login">
                <div class="container__input">
                    <label class="label" for="email">Email</label>
                    <mdicon class="icon__modify" name="email-outline" />
                    <input v-model="user.email" class="form-control" type="email" autocomplete="off" id="email"
                        name="email">
                </div>
                <div class="error">
                    <span v-if="errorsForm['errors'].email" class="span__error">{{
                        errorsForm["errors"].email[0]
                    }}</span>
                </div>
                <div class="container__input">
                    <label class="label" for="password">Password</label>
                    <mdicon class="icon__modify" name="lock" />
                    <input v-model="user.password" class="form-control" type="password" autocomplete="off" id="password"
                        name="password">
                </div>
                <div class="error">
                    <span v-if="errorsForm['errors'].password" class="span__error">{{
                        errorsForm["errors"].password[0]
                    }}</span>
                </div>
                <div class="container__button">
                    <a class="link" href="#">esqueceu sua senha?</a>
                    <button @click.prevent="emits('nextStep')" class="btn__register" href="#">cadastre-se.</button>
                </div>
                <button class="btn btn__submit" type="submit">entrar</button>
            </form>
        </div>
        <div class="container__decription">
            <figure class="figure">
                <img src="@/assets/img/Mr.png" style="width: 200px;" alt="logo" />
                <p class="title__2 mt-5 me-2">
                    Faça seu cadastro
                </p>
            </figure>
            <p class="sub__title__2 mt-5">Insira seus dados pessoais</p>
            <p class="sub__title__2">e comece a jornada conosco</p>
            <button @click="emits('nextStep')" class="btn btn__link">cadastre-se</button>
        </div>
    </div>
</template>

<script setup>
import { useAuth } from "@/stores/auth.js";
import { useRouter } from "vue-router";
import http from "@/services/http.js";
import { ref, reactive } from "vue";
import { userData } from "@/stores/data.js";

let entrar = ref(true);
let none = ref(false);

const auth = useAuth();
const router = useRouter();
const emits = defineEmits(["nextStep"]);

const user = reactive({
    name: "Marcos Rafael",
    email: "marcos@gmail.com",
    password: "123",
});
const errorsForm = reactive({ errors: {} });

async function login() {
    const data = userData();
    try {
        const res = await http.post("/auth", user);
        auth.setToken(res.data.token);
        const resp = await http.post("/me");
        data.setUser(resp.data.user);

        data.setExpenses(resp.data.expenses);
        data.setExpensesMonth(resp.data.expensesMonth);
        data.setValuePayExpenses(resp.data.valuePayExpenses);
        data.setValuePendingExpenses(resp.data.valuePendingExpenses);
        data.setValueTotalExpensesMonth(resp.data.valueTotalExpensesMonth);

        data.setRevenues(resp.data.revenues);
        data.setRevenuesMonth(resp.data.revenuesMonth);
        data.setValueTotalRevenuesMonth(resp.data.valueTotalRevenuesMonth);
        data.setValueReceivedRevenues(resp.data.valueRevenuesReceived);
        data.setValuePendingRevenues(resp.data.valuePendingRevenues);

        data.setTotalCreditCard(resp.data.totalCreditCard);
        data.setTotalBalance(resp.data.totalBalance);
        auth.setUser(resp.data.user.name);
        router.push({ name: "dashboard" });
    } catch (error) {
        console.log(error);
        errorsForm["errors"] = error.response.data;
    }
}
</script>
<style scoped>
.box {
    display: flex;
    box-shadow: 1px 1px 10px 5px #77d08e;
    border-radius: 10px;
    padding: 0;
    width: 80%;
}

.container__dados {
    border-radius: 10px 0 0 10px;
    width: 60%;
    display: flex;
    flex-direction: column;
    justify-items: center;
    padding: 2rem 6rem;
}

.title {
    font-size: 35px;
    font-weight: bold;
    color: #fefefe;
    text-align: center;
}

.title__2 {
    font-size: 40px;
    font-weight: bold;
    color: #515050;
    text-align: center;
}

.social__media {
    margin: 1rem 0;
    display: flex;
    justify-content: center;
    text-align: center;
}

.list__social__media {
    display: flex;
    list-style-type: none;
}

.link__social__media:not(:first-child) {
    margin-left: 10px;
}

.link__social__media .item__social__media {
    transition: background-color .5s;
}

.link__social__media:hover .item__social__media {
    /* background-color: #0097a7; */
    color: #fff !important;
    border-color: #77d08e;
}

.item__social__media {
    border: 1px solid #bdc3c7;
    border-radius: 50%;
    width: 35px;
    height: 35px;
    line-height: 35px;
    text-align: center;
    color: #95a5a6;
}

.icon__modify {
    color: #7f8c8d;
    padding: 0 5px;
}

.sub__title {
    color: #fefefe;
    text-align: center;
}

.sub__title__2 {
    color: #515050;
    text-align: center;

}

.form {
    display: flex;
    flex-direction: column;
    width: 100% !important;
}

.container__input {
    background-color: #1e1e1e;
    margin: 20px 0 0 0;
    display: flex;
    align-items: center;
    padding-left: 5px;
    position: relative;
    border-radius: 5px;
}

.container__input input {
    height: 55px;
    color: #ccc;
    width: 100%;
    border: none;
    background-color: transparent;
}

.container__input input:focus {
    box-shadow: 0 0 0 0.15rem #0096a72f !important;
}

.label {
    color: #fefefe;
    background-color: transparent;
    position: absolute;
    left: 10px;
    top: -30px;
    opacity: 0.4;
    cursor: text;
    transition: 0.5s ease-in-out;
}

.error {
    height: 20px;
}

.span__error {
    color: rgb(194, 4, 4);
    position: relative;
    top: 0;
    left: 0;
}

.container__button {
    text-align: center;
}

.link {
    color: #0097a7;
    font-size: 16px;
    margin: 15px 0;
    text-align: center;
}

.btn__register {
    display: none;
}

.container__decription {
    width: 40%;
    background: #77d08e;
    /* background: #0097a7; */
    border-radius: 0 10px 10px 0;
    display: flex;
    flex-direction: column;
    justify-items: center;
}

.figure {
    display: flex;
    justify-content: space-evenly;
    align-items: center;
    margin-bottom: 50px;
}

.btn {
    border-radius: 15px;
    text-transform: uppercase;
    color: #fff;
    font-size: 10px;
    /* padding: 10px 50px; */
    padding: 5px;
    cursor: pointer;
    font-weight: bold;
    width: 200px;
    align-self: center;
    border: none;
    margin-top: 1rem;
    font-size: 20px;
    background-color: #77d08e;
    border: 1px solid #77d08e;
    transition: background-color .5s;
}

.btn__submit:hover {
    background-color: #e1e1e1;
    border: 1px solid #77d08e;
    color: #77d08e;
}

.btn__link {
    margin-top: 60px;
    background-color: transparent;
    border: 1px solid #fff;
    transition: background-color .5s;
}

.btn__link:hover {
    background-color: #fff;
    color: #58af9b;
}

@media screen and (max-width: 1201px) {

    .box {
        width: 90%;
    }

    .container__dados {
        padding: 2rem 4rem;
    }


}

@media screen and (max-width: 920px) {

    .container__dados {
        width: 100%;
        padding: 2rem 4rem;
    }

    .container__decription {
        display: none;
    }

    .btn__register {
        display: inline;
        color: #0097a7;
        font-size: 16px;
        margin: 15px 0;
        background: transparent;
        border: none;
        /* text-align: center; */
    }

    .container__button {
        display: flex;
        justify-content: space-between;
    }

}

@media screen and (max-width: 740px) {
    .container__dados {
        padding: 2rem 2rem;
    }

    .title {
        font-size: 30px;
    }
}

@media screen and (max-width: 440px) {
    .box {
        width: 95%;
    }

    .container__dados {
        padding: 2rem 1rem;
    }
}
</style>