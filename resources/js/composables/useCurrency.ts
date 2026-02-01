// composables/useCurrency.ts
export function useCurrency() {
    const formatCurrency = (amount: number | null | undefined) => {
        if (!amount) return '₱0.00';
        const formatted =  new Intl.NumberFormat('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
        }).format(amount);

        return `₱ ${formatted}`;
    };

    const formatNumber = (amount: number | null | undefined) => {
        if (!amount) return '0.00';
        new Intl.NumberFormat('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
        }).format(amount);
    };

    return {
        formatCurrency,
        formatNumber,
    };
}