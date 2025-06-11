import './bootstrap';

import Alpine from 'alpinejs';
import { initSwalConfirm } from './components/SwalConfirm';

window.Alpine = Alpine;
window.initSwalConfirm = initSwalConfirm;

Alpine.start();
