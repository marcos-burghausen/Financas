import BBIcon from '@/assets/icons/bb.svg';
import CaixaIcon from '@/assets/icons/caixa.svg';
import NubankIcon from '@/assets/icons/nubank.svg';
import SicrediIcon from '@/assets/icons/sicredi.svg';

import MasterCardIcon from '@/assets/icons/mastercard.svg';
import VisaIcon from '@/assets/icons/visa.svg';

// Mapeia o nome do banco (como está salvo no DB) ao ícone importado
const iconBankMap: { [key: string]: string } = {
  'Sicredi': SicrediIcon,
  'Nubank': NubankIcon,
  'Caixa Economica': CaixaIcon,
  'Banco do Brasil': BBIcon,
};

// Função que retorna o ícone correspondente ou um ícone padrão
export const getBankIcon = (bankName: string): string => {
  return iconBankMap[bankName] || 'mdi-credit-card-outline'; // Retorna um ícone padrão do Vuetify se não encontrar
};

export const iconCardMap = [
  { name: 'MasterCard', icon: MasterCardIcon },
  { name: 'Visa', icon: VisaIcon },
];

// export const getCardIcon = () => {
//   return iconCardMap;
// };