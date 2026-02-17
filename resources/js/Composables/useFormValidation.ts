/**
 * Form validation utilities for authentication pages
 */

export function useFormValidation() {
  /**
   * Validate email format
   */
  const validateEmail = (email: string): boolean => {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
  };

  /**
   * Validate password strength
   * Returns score 0-4:
   * 0: No password
   * 1: Weak (only length)
   * 2: Fair (length + uppercase/lowercase)
   * 3: Good (length + case + number)
   * 4: Strong (length + case + number + special character)
   */
  const calculatePasswordStrength = (password: string): number => {
    if (!password) return 0;

    let strength = 0;

    // At least 8 characters
    if (password.length >= 8) strength++;

    // Uppercase and lowercase letters
    if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;

    // At least one number
    if (/\d/.test(password)) strength++;

    // Special character
    if (/[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(password)) strength++;

    return strength;
  };

  /**
   * Get password strength label
   */
  const getStrengthLabel = (strength: number): string => {
    if (strength === 0) return 'No password';
    if (strength === 1) return 'Weak';
    if (strength === 2) return 'Fair';
    if (strength === 3) return 'Good';
    return 'Strong';
  };

  /**
   * Validate password requirements
   */
  const validatePassword = (password: string): {
    isValid: boolean;
    errors: string[];
  } => {
    const errors: string[] = [];

    if (!password) {
      errors.push('Password is required');
      return { isValid: false, errors };
    }

    if (password.length < 8) {
      errors.push('Password must be at least 8 characters');
    }

    if (!/[a-z]/.test(password)) {
      errors.push('Password must contain a lowercase letter');
    }

    if (!/[A-Z]/.test(password)) {
      errors.push('Password must contain an uppercase letter');
    }

    if (!/\d/.test(password)) {
      errors.push('Password must contain a number');
    }

    return {
      isValid: errors.length === 0,
      errors,
    };
  };

  /**
   * Validate password confirmation
   */
  const validatePasswordConfirmation = (
    password: string,
    confirmation: string
  ): boolean => {
    return password === confirmation && password.length > 0;
  };

  /**
   * Validate required field
   */
  const validateRequired = (value: string): boolean => {
    return value.trim().length > 0;
  };

  /**
   * Sanitize email (trim whitespace)
   */
  const sanitizeEmail = (email: string): string => {
    return email.trim().toLowerCase();
  };

  return {
    validateEmail,
    calculatePasswordStrength,
    getStrengthLabel,
    validatePassword,
    validatePasswordConfirmation,
    validateRequired,
    sanitizeEmail,
  };
}
