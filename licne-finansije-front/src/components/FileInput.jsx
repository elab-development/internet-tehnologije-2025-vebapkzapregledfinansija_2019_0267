import React from 'react';

const FileInput = ({
    id,
    label,
    onChange,
    accept,
    hint,
    required = false,
    ...rest
}) => {
    return (
        <div className="auth-field">
            <label htmlFor={id}>{label}</label>
            <input
                id={id}
                type="file"
                accept={accept}
                onChange={onChange}
                required={required}
                {...rest}
            />
            {hint && <small className="file-input-hint">{hint}</small>}
        </div>
    )
};

export default FileInput;