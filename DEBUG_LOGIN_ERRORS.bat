@echo off
echo 🔍 DEBUG ERREURS DE LOGIN
echo =========================

echo 📍 Pour débugger les erreurs de login:
echo.
echo 1. Ouvrez http://localhost:5174
echo 2. Appuyez sur F12 (DevTools)
echo 3. Onglet Console
echo 4. Essayez de vous connecter avec Franco ou Fatima
echo 5. Regardez les erreurs dans la console
echo.
echo 6. Tapez aussi ces commandes dans la console:
echo.
echo    // Voir les comptes disponibles
echo    console.log('Comptes:', JSON.parse(localStorage.getItem('smarterp_accounts') ^|^| '[]'))
echo.
echo    // Nettoyer localStorage si nécessaire
echo    localStorage.clear()
echo.
echo 7. Copiez-moi les erreurs exactes que vous voyez !
echo.

pause
