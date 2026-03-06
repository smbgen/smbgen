import React, { useState } from 'react';

export default function CssClassWhitelist({ initialClasses = [], onChange }) {
    const [classes, setClasses] = useState(initialClasses);
    const [newClass, setNewClass] = useState('');
    const [filter, setFilter] = useState('');

    const addNewClass = () => {
        if (newClass.trim() && !classes.includes(newClass.trim())) {
            const updated = [...classes, newClass.trim()].sort();
            setClasses(updated);
            onChange(updated);
            setNewClass('');
        }
    };

    const removeClass = (classToRemove) => {
        const updated = classes.filter(c => c !== classToRemove);
        setClasses(updated);
        onChange(updated);
    };

    const loadDefaults = () => {
        // This would load from CmsCompanyColors::getDefaultCssClassWhitelist()
        // For now, we'll make a fetch request
        fetch('/admin/cms/default-css-classes')
            .then(res => res.json())
            .then(data => {
                setClasses(data.classes);
                onChange(data.classes);
            });
    };

    const filteredClasses = classes.filter(c => 
        c.toLowerCase().includes(filter.toLowerCase())
    );

    return (
        <div className="space-y-4">
            {/* Header with actions */}
            <div className="flex items-center justify-between">
                <div>
                    <h3 className="text-lg font-semibold text-gray-100">CSS Class Whitelist</h3>
                    <p className="text-sm text-gray-400">Only these classes will be available to AI content generation</p>
                </div>
                <button 
                    type="button"
                    onClick={loadDefaults}
                    className="btn-secondary text-sm"
                >
                    <i className="fas fa-sync-alt mr-2"></i>
                    Load Defaults
                </button>
            </div>

            {/* Add new class */}
            <div className="flex gap-2">
                <input
                    type="text"
                    value={newClass}
                    onChange={(e) => setNewClass(e.target.value)}
                    onKeyPress={(e) => e.key === 'Enter' && (e.preventDefault(), addNewClass())}
                    placeholder="Add CSS class (e.g., btn-primary, hero, section)"
                    className="form-input flex-1"
                />
                <button 
                    type="button"
                    onClick={addNewClass}
                    className="btn-primary"
                >
                    <i className="fas fa-plus"></i>
                </button>
            </div>

            {/* Filter */}
            <input
                type="text"
                value={filter}
                onChange={(e) => setFilter(e.target.value)}
                placeholder="Filter classes..."
                className="form-input"
            />

            {/* Class count */}
            <div className="text-sm text-gray-400">
                {filteredClasses.length} of {classes.length} classes
                {filter && ` (filtered)`}
            </div>

            {/* Class list */}
            <div className="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2 max-h-96 overflow-y-auto p-4 bg-gray-800/50 rounded-lg border border-gray-700">
                {filteredClasses.map((cssClass) => (
                    <div 
                        key={cssClass}
                        className="flex items-center justify-between bg-gray-700/50 px-3 py-2 rounded border border-gray-600 hover:border-gray-500 transition-colors group"
                    >
                        <code className="text-sm text-blue-400">.{cssClass}</code>
                        <button
                            type="button"
                            onClick={() => removeClass(cssClass)}
                            className="opacity-0 group-hover:opacity-100 text-red-400 hover:text-red-300 transition-opacity"
                            title="Remove class"
                        >
                            <i className="fas fa-times text-xs"></i>
                        </button>
                    </div>
                ))}
                {filteredClasses.length === 0 && (
                    <div className="col-span-full text-center text-gray-500 py-8">
                        {filter ? 'No matching classes found' : 'No classes added yet'}
                    </div>
                )}
            </div>

            {/* Hidden input for form submission */}
            <input 
                type="hidden" 
                name="allowed_css_classes" 
                value={JSON.stringify(classes)} 
            />
        </div>
    );
}
