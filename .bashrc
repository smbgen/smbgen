# Laravel Herd Aliases - please do not remove these lines
alias php="php.bat"
alias herd="herd.bat"
alias laravel="laravel.bat"

# Composer function for Git Bash compatibility
composer() {
    cmd //c "C:\Users\paren\.config\herd\bin\composer.bat $*"
}
export -f composer