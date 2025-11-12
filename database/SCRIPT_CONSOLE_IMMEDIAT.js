// 🚨 SCRIPT DE CORRECTION IMMÉDIATE
// Copiez-collez ce script dans la console F12 de SmartERP Pro

console.log('🚨 CORRECTION IMMÉDIATE EN COURS...')

// 1. Récupérer les données
const currentUser = JSON.parse(localStorage.getItem('smarterp_current_user'))
const allSales = JSON.parse(localStorage.getItem('smarterp_sales'))

if (!currentUser || !allSales) {
    console.error('❌ Données manquantes')
} else {
    console.log('👤 Utilisateur:', currentUser.email)
    
    const userSales = allSales[currentUser.email] || []
    console.log('📊 Ventes à corriger:', userSales.length)
    
    // 2. Corriger chaque vente
    let correctedCount = 0
    userSales.forEach((sale, index) => {
        console.log(`\n--- Vente ${index + 1}: ${sale.id} ---`)
        console.log('Avant:', sale.timestamp)
        
        if (sale.timestamp && sale.timestamp.includes('à')) {
            // Parser la date française CORRECTEMENT
            const [datePart, timePart] = sale.timestamp.split(' à ')
            const [day, month, year] = datePart.split('/')
            const [hour, minute, second] = timePart.split(':')
            
            // CORRECTION CRITIQUE: 10/11/2025 = 10 novembre 2025
            const correctDate = new Date(
                parseInt(year),      // 2025
                parseInt(month) - 1, // 11-1 = 10 (novembre en JS)
                parseInt(day),       // 10 (jour)
                parseInt(hour) || 0,
                parseInt(minute) || 0,
                parseInt(second) || 0
            )
            
            console.log('Correction:', `${day}/${month}/${year} → ${correctDate.toLocaleDateString('fr-FR')}`)
            
            // Sauvegarder en ISO
            sale.timestamp = correctDate.toISOString()
            console.log('Après:', sale.timestamp)
            correctedCount++
        }
    })
    
    // 3. Sauvegarder les corrections
    allSales[currentUser.email] = userSales
    localStorage.setItem('smarterp_sales', JSON.stringify(allSales))
    
    console.log(`\n✅ ${correctedCount} ventes corrigées et sauvegardées`)
    
    // 4. Vérifier le résultat
    const today = new Date()
    today.setHours(0, 0, 0, 0)
    
    let todayRevenue = 0
    let todayTransactions = 0
    
    console.log('\n🔍 VÉRIFICATION:')
    console.log('Aujourd\'hui (référence):', today)
    
    userSales.forEach(sale => {
        const saleDate = new Date(sale.timestamp)
        console.log(`Vente ${sale.id}: ${saleDate} >= ${today} = ${saleDate >= today}`)
        
        if (saleDate >= today) {
            todayRevenue += sale.total
            todayTransactions++
            console.log(`✅ Comptée: +${sale.total} Ar`)
        }
    })
    
    console.log('\n📊 RÉSULTAT FINAL:')
    console.log('CA aujourd\'hui:', todayRevenue.toLocaleString(), 'Ar')
    console.log('Transactions aujourd\'hui:', todayTransactions)
    
    if (todayRevenue > 0) {
        console.log('\n🎉 SUCCESS! Rechargez maintenant la page Rapports (F5)')
    } else {
        console.log('\n❌ Problème persistant - vérifiez les dates')
    }
}

console.log('\n🏁 SCRIPT TERMINÉ')
