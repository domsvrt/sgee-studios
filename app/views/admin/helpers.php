<?php

declare(strict_types=1);

if (!function_exists('admin_selected')) {
    function admin_selected(mixed $actual, mixed $expected): string
    {
        return (string) $actual === (string) $expected ? 'selected' : '';
    }
}

if (!function_exists('admin_checked')) {
    function admin_checked(bool $value): string
    {
        return $value ? 'checked' : '';
    }
}

if (!function_exists('admin_option_tags')) {
    /**
     * @param array<int, string> $options
     */
    function admin_option_tags(array $options, mixed $selected = null, ?callable $labeler = null): void
    {
        $labeler = $labeler ?? static fn (string $value): string => ucfirst($value);
        foreach ($options as $value) {
            $selectedAttr = admin_selected($selected, $value);
            echo '<option value="' . htmlspecialchars($value, ENT_QUOTES, 'UTF-8') . '" ' . $selectedAttr . '>'
                . htmlspecialchars($labeler($value), ENT_QUOTES, 'UTF-8')
                . '</option>';
        }
    }
}

if (!function_exists('admin_render_create_header')) {
    function admin_render_create_header(string $title, string $subtitle, string $target, string $label): void
    {
        $title = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');
        $subtitle = htmlspecialchars($subtitle, ENT_QUOTES, 'UTF-8');
        $target = htmlspecialchars($target, ENT_QUOTES, 'UTF-8');
        $label = htmlspecialchars($label, ENT_QUOTES, 'UTF-8');

        echo '<div class="flex flex-wrap items-center justify-between gap-3">';
        echo '<div><h3 class="admin-panel-title">' . $title . '</h3><p class="admin-panel-subtitle">' . $subtitle . '</p></div>';
        echo '<div class="flex items-center gap-2">';
        echo '<button type="button" class="btn-secondary" data-create-toggle data-target="' . $target . '" data-show-label="' . $label . '" data-hide-label="Hide form">' . $label . '</button>';
        echo '<button type="submit" form="' . $target . '" class="btn-primary hidden" data-create-submit="' . $target . '">' . $label . '</button>';
        echo '</div></div>';
    }
}

if (!function_exists('admin_render_empty_row')) {
    function admin_render_empty_row(int $colspan, string $message): void
    {
        echo '<tr><td colspan="' . $colspan . '" class="px-5 py-12 text-center text-slate-500 dark:text-slate-400">'
            . htmlspecialchars($message, ENT_QUOTES, 'UTF-8')
            . '</td></tr>';
    }
}
