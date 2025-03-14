<template>
  <header class="cabecalho">
    <mdicon
      name="menu"
      class="mdicon"
      size="30"
      @click="$emit('expandirMenu')"
    />
    <div class="container__mes">
      <mdicon
        name="chevron-left"
        class="mdicon"
        size="30"
        @click="$emit('mesAnterior')"
      />
      <span class="mes"> {{ mesReferencia }} </span>
      <mdicon
        name="chevron-right"
        class="mdicon"
        size="30"
        @click="$emit('proximoMes')"
      />
    </div>
    <div class="d-flex justify-space-around">
      <v-menu>
        <template #activator="{ props }">
          <!-- <v-btn
                color="primary"
                v-bind="props"
                >
                Activator slot
                </v-btn> -->
          <mdicon
            v-if="useAuth.isAuthenticated"
            name="dots-vertical"
            class="mdicon ms-3"
            v-bind="props"
            size="30"
          />
        </template>
        <v-list
          style="
            width: 150px;
            background: rgb(38, 38, 39);
            box-shadow: -4px -4px 5px #3e4247, 7px 7px 7px #1d1f23;
            color: #fefefe;
          "
        >
          <v-list-item
            v-for="(item, index) in items"
            :key="index"
            :value="index"
            @click="item.action"
          >
            <v-list-item-title style="font-size: 20px">
              <mdicon :name="item.icon" class="me-3 fs-3" />
              {{ item.title }}
            </v-list-item-title>
          </v-list-item>
        </v-list>
      </v-menu>
      <!-- </div> -->
      <!-- <mdicon
            v-if="auth.isAuthenticated"
            name="account"
            class="mdicon ms-3"
        /> -->
    </div>
  </header>
</template>

<script setup lang="ts">
import { useAuthStore } from "@/store/auth";
import { useRouter } from "vue-router";
import http from "@/services/http";
import { ref } from "vue";
import { useUserStore } from "@/store/user";

const props = defineProps({
  mesReferencia: {
    type: String,
    default: "",
  },
});
// const titulo = computed(() => props.name);
const router = useRouter();
const useAuth = useAuthStore();
const useUser = useUserStore();

let name = ref(useUser.user.name.split(" ")[0]);

const items = ref([{ title: "Sair", icon: "power", action: logout }]);

async function logout() {
  try {
    await http.post("/logout");
    useAuth.clear();
    router.push({ name: "home" });
  } catch (error) {
    // console.log(error);
  }
}
</script>

<style scoped>
.cabecalho {
  padding-inline: 10px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  /* box-shadow: -4px -4px 5px #3e4247, 7px 7px 7px #1d1f23; */
  /* background-color: rgba(0, 0, 0, 0.1); */
  height: 90px;
}

.mdicon {
  /* color: #77d08e; */
  color: #757575;
  cursor: pointer;
  /* padding: 10px; */
  /* box-shadow: -4px -4px 5px #3e4247, 7px 7px 7px #1d1f23; */
  border-radius: 20px;
}
.mes {
  font-size: 25px;
  color: #bdbdbd;
}
.container__mes {
  width: 100%;
  display: flex;
  justify-content: space-around;
}
</style>
