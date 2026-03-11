/** @type {import('tailwindcss').Config} */
export default {
  darkMode: 'class',
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
    "./app/Livewire/**/*.php",
    "./app/Modules/**/*.blade.php",
    "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
  ],
  safelist: [
    // Background colors - all palette colors with shades
    {
      pattern: /^bg-(slate|gray|zinc|neutral|stone|red|orange|amber|yellow|lime|green|emerald|teal|cyan|sky|blue|indigo|violet|purple|fuchsia|pink|rose)-(50|100|200|300|400|500|600|700|800|900)$/
    },
    {
      pattern: /^bg-(white|black|transparent|current)$/
    },
    // Text colors
    {
      pattern: /^text-(slate|gray|zinc|neutral|stone|red|orange|amber|yellow|lime|green|emerald|teal|cyan|sky|blue|indigo|violet|purple|fuchsia|pink|rose)-(50|100|200|300|400|500|600|700|800|900)$/
    },
    {
      pattern: /^text-(white|black|transparent|current)$/
    },
    // Border colors
    {
      pattern: /^border-(slate|gray|zinc|neutral|stone|red|orange|amber|yellow|lime|green|emerald|teal|cyan|sky|blue|indigo|violet|purple|fuchsia|pink|rose)-(50|100|200|300|400|500|600|700|800|900)$/
    },
    // Gradients
    {
      pattern: /^(from|via|to)-(slate|gray|zinc|neutral|stone|red|orange|amber|yellow|lime|green|emerald|teal|cyan|sky|blue|indigo|violet|purple|fuchsia|pink|rose)-(50|100|200|300|400|500|600|700|800|900)$/
    },
    // Spacing - padding and margins
    {
      pattern: /^(p|px|py|pt|pb|pl|pr|m|mx|my|mt|mb|ml|mr)-(0|1|2|3|4|5|6|7|8|9|10|11|12|14|16|20|24|28|32|36|40|44|48|52|56|60|64|72|80|96)$/
    },
    // Width and height
    {
      pattern: /^(w|h)-(0|1|2|3|4|5|6|7|8|9|10|11|12|14|16|20|24|28|32|36|40|44|48|52|56|60|64|72|80|96|auto|full|screen|min|max|fit)$/
    },
    {
      pattern: /^(w|h)-(1\/2|1\/3|2\/3|1\/4|2\/4|3\/4|1\/5|2\/5|3\/5|4\/5|1\/6|2\/6|3\/6|4\/6|5\/6)$/
    },
    // Min/max width and height
    {
      pattern: /^(min-w|max-w|min-h|max-h)-(0|full|min|max|fit|prose|none|xs|sm|md|lg|xl|2xl|3xl|4xl|5xl|6xl|7xl)$/
    },
    // Display
    'block', 'inline-block', 'inline', 'flex', 'inline-flex', 'table', 'inline-table', 'table-caption', 'table-cell', 'table-column', 'table-column-group', 'table-footer-group', 'table-header-group', 'table-row-group', 'table-row', 'flow-root', 'grid', 'inline-grid', 'contents', 'list-item', 'hidden',
    // Flex properties
    {
      pattern: /^(flex-row|flex-row-reverse|flex-col|flex-col-reverse|flex-wrap|flex-wrap-reverse|flex-nowrap|items-start|items-end|items-center|items-baseline|items-stretch|justify-start|justify-end|justify-center|justify-between|justify-around|justify-evenly|content-start|content-end|content-center|content-between|content-around|content-evenly|self-auto|self-start|self-end|self-center|self-stretch|self-baseline)$/
    },
    {
      pattern: /^(flex-1|flex-auto|flex-initial|flex-none|grow|grow-0|shrink|shrink-0)$/
    },
    // Grid
    {
      pattern: /^(grid-cols-1|grid-cols-2|grid-cols-3|grid-cols-4|grid-cols-5|grid-cols-6|grid-cols-7|grid-cols-8|grid-cols-9|grid-cols-10|grid-cols-11|grid-cols-12|grid-cols-none)$/
    },
    {
      pattern: /^(col-auto|col-span-1|col-span-2|col-span-3|col-span-4|col-span-5|col-span-6|col-span-7|col-span-8|col-span-9|col-span-10|col-span-11|col-span-12|col-span-full)$/
    },
    {
      pattern: /^(grid-rows-1|grid-rows-2|grid-rows-3|grid-rows-4|grid-rows-5|grid-rows-6|row-auto|row-span-1|row-span-2|row-span-3|row-span-4|row-span-5|row-span-6|row-span-full)$/
    },
    // Gap and space
    {
      pattern: /^(gap|gap-x|gap-y|space-x|space-y)-(0|1|2|3|4|5|6|7|8|9|10|11|12|14|16|20|24|28|32|36|40|44|48|52|56|60|64|72|80|96)$/
    },
    // Typography
    {
      pattern: /^text-(xs|sm|base|lg|xl|2xl|3xl|4xl|5xl|6xl|7xl|8xl|9xl)$/
    },
    {
      pattern: /^font-(thin|extralight|light|normal|medium|semibold|bold|extrabold|black)$/
    },
    {
      pattern: /^(leading-3|leading-4|leading-5|leading-6|leading-7|leading-8|leading-9|leading-10|leading-none|leading-tight|leading-snug|leading-normal|leading-relaxed|leading-loose)$/
    },
    {
      pattern: /^(tracking-tighter|tracking-tight|tracking-normal|tracking-wide|tracking-wider|tracking-widest)$/
    },
    'text-left', 'text-center', 'text-right', 'text-justify', 'uppercase', 'lowercase', 'capitalize', 'normal-case',
    // Borders and rounded
    {
      pattern: /^(rounded|rounded-t|rounded-r|rounded-b|rounded-l|rounded-tl|rounded-tr|rounded-br|rounded-bl)-(none|sm|md|lg|xl|2xl|3xl|full)$/
    },
    {
      pattern: /^border-(0|2|4|8|t|r|b|l|t-0|r-0|b-0|l-0|t-2|r-2|b-2|l-2|t-4|r-4|b-4|l-4|t-8|r-8|b-8|l-8)$/
    },
    'border', 'border-solid', 'border-dashed', 'border-dotted', 'border-double', 'border-hidden', 'border-none',
    // Shadows
    {
      pattern: /^shadow-(sm|md|lg|xl|2xl|inner|none)$/
    },
    'shadow',
    // Position and layout
    'static', 'fixed', 'absolute', 'relative', 'sticky',
    {
      pattern: /^(inset|top|right|bottom|left)-(0|1|2|3|4|5|6|8|10|12|16|20|24|32|40|48|56|64|auto|full)$/
    },
    {
      pattern: /^z-(0|10|20|30|40|50|auto)$/
    },
    // Opacity
    {
      pattern: /^opacity-(0|5|10|20|25|30|40|50|60|70|75|80|90|95|100)$/
    },
    // Transitions and animations
    {
      pattern: /^transition-(none|all|colors|opacity|shadow|transform)$/
    },
    {
      pattern: /^duration-(75|100|150|200|300|500|700|1000)$/
    },
    {
      pattern: /^ease-(linear|in|out|in-out)$/
    },
    // Overflow and object fit
    'overflow-auto', 'overflow-hidden', 'overflow-visible', 'overflow-scroll', 'overflow-x-auto', 'overflow-x-hidden', 'overflow-x-visible', 'overflow-x-scroll', 'overflow-y-auto', 'overflow-y-hidden', 'overflow-y-visible', 'overflow-y-scroll',
    'object-contain', 'object-cover', 'object-fill', 'object-none', 'object-scale-down',
    // Aspect ratio
    'aspect-auto', 'aspect-square', 'aspect-video',
    // Common hero/layout classes
    'container', 'mx-auto', 'max-w-7xl', 'max-w-6xl', 'max-w-5xl', 'max-w-4xl', 'max-w-3xl', 'max-w-2xl', 'max-w-xl', 'max-w-lg', 'max-w-md', 'max-w-sm', 'max-w-xs',
    // Background gradients
    'bg-gradient-to-r', 'bg-gradient-to-l', 'bg-gradient-to-t', 'bg-gradient-to-b', 'bg-gradient-to-tr', 'bg-gradient-to-tl', 'bg-gradient-to-br', 'bg-gradient-to-bl',
    // Interactive states (hover, focus, etc.)
    {
      pattern: /^hover:(bg|text|border)-(slate|gray|zinc|neutral|stone|red|orange|amber|yellow|lime|green|emerald|teal|cyan|sky|blue|indigo|violet|purple|fuchsia|pink|rose)-(50|100|200|300|400|500|600|700|800|900)$/
    },
    {
      pattern: /^focus:(bg|text|border)-(slate|gray|zinc|neutral|stone|red|orange|amber|yellow|lime|green|emerald|teal|cyan|sky|blue|indigo|violet|purple|fuchsia|pink|rose)-(50|100|200|300|400|500|600|700|800|900)$/
    },
    'hover:shadow-lg', 'hover:shadow-xl', 'hover:shadow-2xl', 'focus:outline-none', 'focus:ring-2', 'focus:ring-4',
    // Transform and scale
    'transform', 'scale-95', 'scale-100', 'scale-105', 'scale-110', 'hover:scale-105', 'hover:scale-110',
    // Custom classes that might be used in CMS
    'hero', 'prose', 'cta-section', 'feature-grid', 'feature-card', 'btn-primary', 'btn-secondary', 'btn-accent'
  ],
  theme: {
    extend: {
      colors: {
        primary: {
          50: '#eff6ff',
          100: '#dbeafe',
          200: '#bfdbfe',
          300: '#93c5fd',
          400: '#60a5fa',
          500: '#3b82f6',
          600: '#2563eb',
          700: '#1d4ed8',
          800: '#1e40af',
          900: '#1e3a8a',
        },
        secondary: {
          50: '#faf5ff',
          100: '#f3e8ff',
          200: '#e9d5ff',
          300: '#d8b4fe',
          400: '#c084fc',
          500: '#a855f7',
          600: '#8b5cf6',
          700: '#7c3aed',
          800: '#6d28d9',
          900: '#5b21b6',
        },
        gray: {
          850: '#1a222e',
          950: '#0a0e16',
        },
      },
    },
  },
  plugins: [
    require('@tailwindcss/forms'),
    require('@tailwindcss/typography'),
  ],
}
