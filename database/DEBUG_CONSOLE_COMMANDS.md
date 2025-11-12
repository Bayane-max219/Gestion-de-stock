# 🔍 COMMANDES DE DEBUG CONSOLE

## 🚨 **URGENT - Diagnostic Immédiat**

### **Étape 1 : Vérifier les ventes sauvegardées**
Ouvrez la console (F12) et tapez :

```javascript
// 1. Vérifier l'utilisateur connecté
const currentUser = JSON.parse(localStorage.getItem('smarterp_current_user'))
console.log('Utilisateur:', currentUser.email)

// 2. Vérifier les ventes sauvegardées
const allSales = JSON.parse(localStorage.getItem('smarterp_sales'))
console.log('Toutes les ventes:', allSales)

// 3. Vérifier les ventes de l'utilisateur
const userSales = allSales[currentUser.email] || []
console.log('Mes ventes:', userSales.length)
console.log('Détail:', userSales)

// 4. Vérifier la dernière vente
if (userSales.length > 0) {
    const lastSale = userSales[userSales.length - 1]
    console.log('Dernière vente:', lastSale.id, lastSale.total, lastSale.timestamp)
}
```

### **Étape 2 : Tester le calcul des rapports**
```javascript
// Calculer manuellement comme ReportsPage
const now = new Date()
const today = new Date(now.getFullYear(), now.getMonth(), now.getDate())
console.log('Aujourd\'hui (début):', today)

let todayRevenue = 0
let todayTransactions = 0

userSales.forEach(sale => {
    console.log('--- Vente:', sale.id, '---')
    console.log('Timestamp original:', sale.timestamp)
    
    // Parser la date comme dans ReportsPage
    let saleDate
    if (sale.timestamp.includes('à')) {
        const [datePart, timePart] = sale.timestamp.split(' à ')
        const [day, month, year] = datePart.split('/')
        const timeparts = timePart.split(':').map(Number)
        saleDate = new Date(parseInt(year), parseInt(month) - 1, parseInt(day), 
                           timeparts[0] || 0, timeparts[1] || 0, timeparts[2] || 0)
    } else {
        saleDate = new Date(sale.timestamp)
    }
    
    console.log('Date parsée:', saleDate)
    console.log('Est >= aujourd\'hui?', saleDate >= today)
    
    if (saleDate >= today) {
        todayRevenue += sale.total
        todayTransactions += 1
        console.log('✅ Comptée dans aujourd\'hui')
    } else {
        console.log('❌ Pas comptée dans aujourd\'hui')
    }
})

console.log('=== RÉSULTAT ===')
console.log('CA aujourd\'hui:', todayRevenue, 'Ar')
console.log('Transactions aujourd\'hui:', todayTransactions)
```

### **Étape 3 : Si les ventes ne sont pas sauvegardées**
```javascript
// Vérifier si la fonction saveSale existe et fonctionne
console.log('localStorage smarterp_sales:', localStorage.getItem('smarterp_sales'))

// Si vide, créer une vente de test
const testSale = {
    id: 'TEST-' + Date.now(),
    items: [{
        id: 1,
        name: 'Test Produit',
        price: 5000,
        quantity: 1
    }],
    total: 5000,
    timestamp: new Date().toISOString(),
    paymentMethod: 'cash'
}

// Sauvegarder manuellement
const currentUser = JSON.parse(localStorage.getItem('smarterp_current_user'))
const allSales = JSON.parse(localStorage.getItem('smarterp_sales') || '{}')
if (!allSales[currentUser.email]) {
    allSales[currentUser.email] = []
}
allSales[currentUser.email].push(testSale)
localStorage.setItem('smarterp_sales', JSON.stringify(allSales))

console.log('✅ Vente de test créée')
```

### **Étape 4 : Forcer le rechargement des rapports**
```javascript
// Aller dans ReportsPage et forcer le rechargement
// (Faire cela après avoir ouvert la page Rapports)
window.location.reload()
```

## 🔧 **Actions de Correction Rapide**

### **Si aucune vente n'est sauvegardée :**
Le problème est dans SalesPage - la fonction `saveSale()` ne fonctionne pas.

### **Si les ventes sont sauvegardées mais mal datées :**
Utiliser l'outil `CORRECTION_DATES_VENTES.html` pour corriger.

### **Si les ventes sont OK mais ReportsPage ne calcule pas :**
Le problème est dans la fonction `calculateReportData()`.

## 🚨 **Solution d'Urgence**

Si rien ne marche, tapez dans la console :

```javascript
// Supprimer toutes les données et recommencer
localStorage.removeItem('smarterp_sales')
localStorage.removeItem('smarterp_products')
console.log('🗑️ Données supprimées - Refaites une vente de test')
```

Puis refaites une vente simple et vérifiez immédiatement les rapports.
