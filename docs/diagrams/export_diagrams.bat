@echo off
set DRAWIO=D:\drawio-skill\draw.io\draw.io.exe
set DIAGRAMS=%~dp0

echo Exporting EngPath diagrams...
echo.

"%DRAWIO%" -x -f png -e -s 2 -o "%DIAGRAMS%architecture.drawio.png" "%DIAGRAMS%architecture.drawio" 2>nul && echo [OK] architecture || echo [FAIL] architecture
"%DRAWIO%" -x -f png -e -s 2 -o "%DIAGRAMS%usecase.drawio.png" "%DIAGRAMS%usecase.drawio" 2>nul && echo [OK] usecase || echo [FAIL] usecase
"%DRAWIO%" -x -f png -e -s 2 -o "%DIAGRAMS%usecase_overview.drawio.png" "%DIAGRAMS%usecase_overview.drawio" 2>nul && echo [OK] usecase_overview || echo [FAIL] usecase_overview
"%DRAWIO%" -x -f png -e -s 2 -o "%DIAGRAMS%erd.drawio.png" "%DIAGRAMS%erd.drawio" 2>nul && echo [OK] erd || echo [FAIL] erd
"%DRAWIO%" -x -f png -e -s 2 -o "%DIAGRAMS%sequence_login.drawio.png" "%DIAGRAMS%sequence_login.drawio" 2>nul && echo [OK] sequence_login || echo [FAIL] sequence_login
"%DRAWIO%" -x -f png -e -s 2 -o "%DIAGRAMS%sequence_speaking.drawio.png" "%DIAGRAMS%sequence_speaking.drawio" 2>nul && echo [OK] sequence_speaking || echo [FAIL] sequence_speaking
"%DRAWIO%" -x -f png -e -s 2 -o "%DIAGRAMS%sequence_purchase.drawio.png" "%DIAGRAMS%sequence_purchase.drawio" 2>nul && echo [OK] sequence_purchase || echo [FAIL] sequence_purchase
"%DRAWIO%" -x -f png -e -s 2 -o "%DIAGRAMS%state_diagrams.drawio.png" "%DIAGRAMS%state_diagrams.drawio" 2>nul && echo [OK] state_diagrams || echo [FAIL] state_diagrams

echo.
echo Done! PNGs saved to: %DIAGRAMS%
pause
