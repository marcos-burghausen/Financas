import BBIcon from "@/assets/icons/bb.svg";
import CaixaIcon from "@/assets/icons/caixa.svg";
import NubankIcon from "@/assets/icons/nubank.svg";
import SicrediIcon from "@/assets/icons/sicredi.svg";
import { DefineComponent } from 'vue';

import MasterCardIcon from "@/assets/icons/mastercard.svg";
import VisaIcon from "@/assets/icons/visa.svg";

// Mapeia o nome do banco (como está salvo no DB) ao ícone importado
const iconBankMap: { [key: string]: DefineComponent<{}, {}, any> } = {
  "Sicredi": SicrediIcon,
  "Nubank": NubankIcon,
  "Caixa Economica": CaixaIcon,
  "Banco do Brasil": BBIcon,
  "MasterCard": MasterCardIcon,
  "Visa": VisaIcon,
};

// Função que retorna o ícone correspondente ou um ícone padrão
export const getBankIcon = (bankName: string): DefineComponent<{}, {}, any> | string => {
  return iconBankMap[bankName] || "mdi-credit-card-outline";
};

export const iconsBank = [
  { name: "Sicredi", value: 'SicrediIcon', icon: SicrediIcon },
  { name: "Nubank", value: 'NubankIcon', icon: NubankIcon },
  { name: "Caixa Economica", value: 'CaixaIcon', icon: CaixaIcon },
  { name: "Banco do Brasil", value: 'BBIcon', icon: BBIcon },
];

export const iconCardMap = [
  { name: "MasterCard", icon: MasterCardIcon },
  { name: "Visa", icon: VisaIcon },
];

// export const getCardIcon = () => {
//   return iconCardMap;
// };