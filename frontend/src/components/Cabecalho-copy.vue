<template>
  <header class="topbar-nav">
   <nav class="navbar navbar-expand fixed-top">
    <ul class="navbar-nav mr-auto align-items-center">
      <li class="nav-item">
        <a class="nav-link toggle-menu" href="javascript:void();">
         <i class="fs-3 menu-icon bi bi-list"></i>
       </a>
      </li>
      <li class="nav-item">
        <form class="search-bar">
          <input type="text" class="form-control" placeholder="Enter keywords">
           <a href="javascript:void();"><i class="bi bi-search"></i></a>
        </form>
      </li>
    </ul>
       
    <ul class="navbar-nav align-items-center right-nav-link">
      <li class="nav-item dropdown-lg">
        <a class="nav-link dropdown-toggle dropdown-toggle-nocaret waves-effect" data-toggle="dropdown" href="javascript:void();">
            <i class="fs-3 bi bi-chat-left-text"></i>
            <span class="badge badge-number">3</span>
        </a>
      </li>
      <li class="nav-item dropdown-lg">
        <a class="nav-link dropdown-toggle dropdown-toggle-nocaret waves-effect" data-toggle="dropdown" href="javascript:void();">
        <i class="fs-3 bi bi-bell"></i></a>
      </li>
      <li class="nav-item">
        <a class="nav-link dropdown-toggle dropdown-toggle-nocaret" data-toggle="dropdown" href="#">
          <span class="user-profile"><img src="../assets/img/profile-img.jpg" class="img-circle" alt="user avatar"></span>
        </a>
        <ul class="dropdown-menu dropdown-menu-right">
         <li class="dropdown-item user-details">
          <a href="javaScript:void();">
             <div class="media">
               <div class="avatar"><img class="align-self-start mr-3" src="https://via.placeholder.com/110x110" alt="user avatar"></div>
              <div class="media-body">
              <h6 class="mt-2 user-title">{{ $nome }}</h6>
              <p class="user-subtitle">mccoy@example.com</p>
              </div>
             </div>
            </a>
          </li>
          <li class="dropdown-divider"></li>
          <li class="dropdown-item"><i class="icon-envelope mr-2"></i> Inbox</li>
          <li class="dropdown-divider"></li>
          <li class="dropdown-item"><i class="icon-wallet mr-2"></i><a href="{{ route('profile') }}"> Conta</a></li>
          <li class="dropdown-divider"></li>
          <li class="dropdown-item"><i class="icon-settings mr-2"></i> Configurações</li>
          <li class="dropdown-divider"></li>
          <li class="dropdown-item"><i class="icon-power mr-2"></i><a href="{{ route('logout') }}"> Sair</a></li>
        </ul>
      </li>
    </ul>
  </nav>
  </header>
</template>

<script setup>
import http from "@/services/http.js";
import { useAuth } from "@/stores/auth.js";
import { useRouter } from "vue-router";
import { userData } from "@/stores/data.js";
import { computed } from "vue";

const props = defineProps({
  name: String
});
const titulo = computed(() => props.name);

const router = useRouter();
const auth = useAuth();
const data = userData();



async function logout() {
  try {
    const { data } = await http.post("/logout");
    console.log(data);
    auth.clear();
    router.push({ name: "home" });
  } catch (error) {
    console.log(error);
  }
}
</script>

<style scoped>
header {
  margin: 0 0 15px 0;
  display: flex;
  align-items: center;
  justify-content: flex-end;
  box-shadow: -4px -4px 5px #3e4247, 7px 7px 7px #1d1f23;
  height: 60px;
}
header .router-link {
  color: #0097a7;
  margin-right: 15px;
  text-decoration: none;
  font-size: 20px;
}
/* .btn-expandir {
  color: #0097a7;
  cursor: pointer;
  margin: 20px;
  padding: 10px;
  box-shadow: -4px -4px 5px #3e4247, 7px 7px 7px #1d1f23;
  border-radius: 20px;
} */
</style>