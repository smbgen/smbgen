import React, { useRef, useEffect } from 'react';
import Editor from '@monaco-editor/react';

export default function MonacoEditor({ 
    value, 
    onChange, 
    language = 'css', 
    height = '400px',
    theme = 'vs-dark',
    readOnly = false 
}) {
    const editorRef = useRef(null);

    function handleEditorDidMount(editor, monaco) {
        editorRef.current = editor;
        
        // Configure editor options
        editor.updateOptions({
            minimap: { enabled: false },
            fontSize: 14,
            lineNumbers: 'on',
            roundedSelection: true,
            scrollBeyondLastLine: false,
            readOnly: readOnly,
            automaticLayout: true,
            tabSize: 2,
            wordWrap: 'on',
        });
    }

    function handleEditorChange(value) {
        if (onChange) {
            onChange(value);
        }
    }

    return (
        <div className="border border-gray-700 rounded-lg overflow-hidden">
            <Editor
                height={height}
                language={language}
                value={value}
                theme={theme}
                onChange={handleEditorChange}
                onMount={handleEditorDidMount}
                options={{
                    readOnly: readOnly,
                    minimap: { enabled: false },
                }}
            />
        </div>
    );
}
