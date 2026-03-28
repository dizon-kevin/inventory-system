.notifications-menu {
    position: relative;
}

.notifications-trigger {
    position: relative;
}

.notifications-badge {
    position: absolute;
    top: -4px;
    right: -4px;
    min-width: 16px;
    height: 16px;
    padding: 0 4px;
    border-radius: 999px;
    background: #dc2626;
    color: #ffffff;
    font-size: 10px;
    font-weight: 700;
    line-height: 16px;
    display: none;
    align-items: center;
    justify-content: center;
    pointer-events: none;
}

.notifications-badge.is-visible {
    display: inline-flex;
}

.notifications-dropdown {
    position: absolute;
    top: calc(100% + 10px);
    right: 0;
    width: 320px;
    max-height: 420px;
    background: #ffffff;
    border: 1px solid rgba(12,26,20,0.08);
    border-radius: 12px;
    box-shadow: 0 12px 36px rgba(0,0,0,0.12);
    opacity: 0;
    transform: translateY(-8px) scale(0.98);
    pointer-events: none;
    transition: opacity .18s ease, transform .18s ease;
    z-index: 240;
    overflow: hidden;
    display: flex;
    flex-direction: column;
}

.notifications-menu.open .notifications-dropdown {
    opacity: 1;
    transform: translateY(0) scale(1);
    pointer-events: auto;
}

.notifications-dropdown-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 12px;
    padding: 14px 16px 12px;
    border-bottom: 1px solid rgba(12,26,20,0.08);
    background: #ffffff;
}

.notifications-dropdown-title {
    font-size: .92rem;
    font-weight: 700;
    color: var(--tp);
}

.notifications-dropdown-subtitle {
    margin-top: 2px;
    font-size: .72rem;
    color: var(--tm);
}

.notifications-mark-all {
    border: none;
    background: transparent;
    padding: 0;
    color: #00a878;
    font-family: 'Sora', sans-serif;
    font-size: .74rem;
    font-weight: 600;
    cursor: pointer;
    white-space: nowrap;
}

.notifications-mark-all:disabled {
    opacity: .45;
    cursor: default;
}

.notifications-list {
    overflow-y: auto;
    max-height: 360px;
    padding: 8px;
}

.notifications-list::-webkit-scrollbar {
    width: 8px;
}

.notifications-list::-webkit-scrollbar-thumb {
    background: rgba(12,26,20,0.14);
    border-radius: 999px;
}

.notifications-empty {
    padding: 28px 16px;
    text-align: center;
    color: var(--tm);
    font-size: .78rem;
}

.notifications-item {
    display: grid;
    grid-template-columns: 10px 34px minmax(0, 1fr) auto;
    gap: 10px;
    align-items: start;
    padding: 12px;
    border-radius: 10px;
    transition: background .15s ease;
}

.notifications-item + .notifications-item {
    margin-top: 6px;
}

.notifications-item.is-unread {
    background: rgba(0,212,170,0.04);
}

.notifications-unread-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #00d4aa;
    margin-top: 6px;
}

.notifications-unread-dot.is-read {
    background: transparent;
}

.notifications-item-icon {
    width: 34px;
    height: 34px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #ffffff;
    flex-shrink: 0;
}

.notifications-item-icon svg {
    width: 15px;
    height: 15px;
}

.notifications-item-body {
    min-width: 0;
}

.notifications-item-title {
    font-size: .8rem;
    font-weight: 700;
    color: var(--tp);
    line-height: 1.3;
}

.notifications-item-description {
    margin-top: 3px;
    font-size: .74rem;
    color: var(--ts);
    line-height: 1.45;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.notifications-item-time {
    font-size: .68rem;
    color: var(--tm);
    white-space: nowrap;
    padding-top: 2px;
}

.notifications-trigger.is-shaking {
    animation: storixBellShake .55s ease;
}

@keyframes storixBellShake {
    0%, 100% { transform: rotate(0deg); }
    20% { transform: rotate(-14deg); }
    40% { transform: rotate(12deg); }
    60% { transform: rotate(-9deg); }
    80% { transform: rotate(7deg); }
}
