// 🔧 MIGRATION AUTOMATIQUE DES DATES DE VENTES
// Exécuter ce script dans la console F12 de SmartERP Pro

console.log('🚀 DÉBUT DE LA MIGRATION AUTOMATIQUE...')

// 1. Récupérer l'utilisateur connecté
const currentUser = JSON.parse(localStorage.getItem('smarterp_current_user'))
if (!currentUser) {
    console.error('❌ Aucun utilisateur connecté')
    throw new Error('Utilisateur non connecté')
}

console.log('👤 Utilisateur:', currentUser.email)

// 2. Récupérer toutes les ventes
const allSales = JSON.parse(localStorage.getItem('smarterp_sales') || '{}')
const userSales = allSales[currentUser.email] || []

console.log('📊 Nombre de ventes à migrer:', userSales.length)

if (userSales.length === 0) {
    console.log('✅ Aucune vente à migrer')
} else {
    let migratedCount = 0
    let totalRevenue = 0

    // 3. Migrer chaque vente
    userSales.forEach((sale, index) => {
        console.log(`\n--- Migration vente ${index + 1}: ${sale.id} ---`)
        console.log('Timestamp original:', sale.timestamp)
        
        if (sale.timestamp && sale.timestamp.includes('à')) {
            // Format français problématique : "10/11/2025 à 12:03:45"
            const [datePart, timePart] = sale.timestamp.split(' à ')
            const [day, month, year] = datePart.split('/')
            const [hour, minute, second] = timePart.split(':')
            
            // CORRECTION: 10/11/2025 = 10 novembre 2025 (pas 11 octobre)
            const correctDate = new Date(
                parseInt(year), 
                parseInt(month) - 1,  // 11-1 = 10 (novembre)
                parseInt(day),        // 10 (jour)
                parseInt(hour) || 0, 
                parseInt(minute) || 0, 
                parseInt(second) || 0
            )
            
            // Sauvegarder en format ISO pour éviter les futurs problèmes
            const oldTimestamp = sale.timestamp
            sale.timestamp = correctDate.toISOString()
            
            console.log('✅ Migré:', oldTimestamp, '→', correctDate.toLocaleDateString('fr-FR'))
            console.log('   Format ISO:', sale.timestamp)
            
            migratedCount++
        } else {
            console.log('⏭️ Déjà au bon format ou format ISO')
        }
        
        totalRevenue += sale.total
        console.log('💰 Montant:', sale.total.toLocaleString(), 'Ar')
    })

    // 4. Sauvegarder les données migrées
    allSales[currentUser.email] = userSales
    localStorage.setItem('smarterp_sales', JSON.stringify(allSales))

    console.log('\n🎉 MIGRATION TERMINÉE !')
    console.log('📊 Résumé:')
    console.log('  - Ventes migrées:', migratedCount)
    console.log('  - Total ventes:', userSales.length)
    console.log('  - CA total:', totalRevenue.toLocaleString(), 'Ar')

    // 5. Vérifier que les ventes comptent maintenant pour aujourd'hui
    const today = new Date()
    today.setHours(0, 0, 0, 0)
    
    let todayRevenue = 0
    let todayTransactions = 0
    
    userSales.forEach(sale => {
        const saleDate = new Date(sale.timestamp)
        if (saleDate >= today) {
            todayRevenue += sale.total
            todayTransactions++
        }
    })

    console.log('\n📈 VÉRIFICATION RAPPORTS:')
    console.log('  - CA aujourd\'hui:', todayRevenue.toLocaleString(), 'Ar')
    console.log('  - Transactions aujourd\'hui:', todayTransactions)
    
    if (todayRevenue > 0) {
        console.log('✅ SUCCESS! Vos rapports vont maintenant afficher les bonnes données')
        console.log('🔄 Rechargez la page Rapports pour voir le résultat')
    } else {
        console.log('⚠️ Aucune vente pour aujourd\'hui après migration')
    }
}

console.log('\n🏁 MIGRATION AUTOMATIQUE TERMINÉE')
console.log('👉 Prochaine étape: Aller dans Rapports & Analyses et recharger (F5)')
