export function translateState(state) {
    switch (state) {
        case 'new': return "Neu eingetroffen";
        case 'confirmed': return "Bestätigt";
        case 'cancelled': return "Storniert";
        case 'shipped': return "Versendet";
        default: return "";
    }
}