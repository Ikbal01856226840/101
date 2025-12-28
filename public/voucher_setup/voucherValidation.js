function isValidNumberInput(event) {
    let keypress = event.which;
    return (
        (keypress >= 48 && keypress <= 57) || // Numbers (0-9)
        (keypress >= 96 && keypress <= 105) || // Numeric keypad (0-9)
        keypress === 46 || // Decimal point (.)
        keypress === 8
    ); // Backspace
}
