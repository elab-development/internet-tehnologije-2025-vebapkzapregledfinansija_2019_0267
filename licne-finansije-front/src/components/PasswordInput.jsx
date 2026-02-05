import React from 'react'
import { useState } from 'react';
import { FiEye, FiEyeOff } from "react-icons/fi";

const PasswordInput = ({placeholder, value, onChange}) => {
  const [showPassword, setShowPassword] = useState(false);
  return (
    <div style={{ position: "relative" }}>
        <input
            type={showPassword ? "text" : "password"}
            placeholder={placeholder}
            value={value}
            onChange={onChange}
            required
        />
        <span
            onClick={() => setShowPassword(!showPassword)}
            style={{ position: "absolute", right: "10px", top: "50%", transform: "translateY(-50%)", cursor: "pointer", color: "#000dff" }}
        >
            {showPassword ? <FiEyeOff size={18} /> : <FiEye size={18} />}
        </span>
      
    </div>
  )
}

export default PasswordInput
