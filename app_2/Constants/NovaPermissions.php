<?php

namespace App\Constants;

//ACL build on top of https://docs.google.com/spreadsheets/d/10oY8vLots-5dF8XrvfxweIyq2IY0DriLIOrT9xcZ-5c/edit?usp=sharing
class NovaPermissions
{
    const NOVA_ACCESS = 'nova.access';

    const USERS_VIEW = 'nova.users.view';
    const USERS_CREATE = 'nova.users.create';
    const USERS_UPDATE = 'nova.users.update';
    const USERS_DELETE = 'nova.users.delete';

    const PERFORMANCE_NOTES_VIEW = 'nova.performance-notes.view';
    const MEDICAL_NOTES_VIEW = 'nova.medical-notes.view';
    const PRIVACY_NOTES_VIEW = 'nova.privacy-notes.view';
    const COACH_REPORT_VIEW = 'nova.coach-reports.view';

    const ROLES_CREATE = 'nova.roles.create';
    const ROLES_UPDATE = 'nova.roles.update';
    const ROLES_VIEW = 'nova.roles.view';
    const ROLES_DELETE = 'nova.roles.delete';

    const PERMISSIONS_CREATE = 'nova.permissions.create';
    const PERMISSIONS_UPDATE = 'nova.permissions.update';
    const PERMISSIONS_VIEW = 'nova.permissions.view';
    const PERMISSIONS_DELETE = 'nova.permissions.delete';

    const EVENT_TEMPLATES_VIEW = 'nova.event-templates.view';
    const EVENT_TEMPLATES_UPDATE = 'nova.event-templates.update';
    const EVENT_TEMPLATES_CREATE = 'nova.event-templates.create';
    const EVENT_TEMPLATES_DELETE = 'nova.event-templates.delete';

    const PLAYERS_CREATE = 'nova.players.create';
    const PLAYERS_UPDATE = 'nova.players.update';
    const PLAYERS_DELETE = 'nova.players.delete';
    const PLAYERS_VIEW = 'nova.players.view';

    const INJURY_VIEW = 'nova.injury.view';
    const INJURY_CREATE = 'nova.nova.injury.create';
    const INJURY_UPDATE = 'nova.injury.update';
    const INJURY_DELETE = 'nova.injury.delete';

    const PLAYER_GROUPS_VIEW = 'nova.player-groups.view';
    const PLAYER_GROUPS_CREATE = 'nova.player-groups.create';
    const PLAYER_GROUPS_UPDATE = 'nova.player-groups.update';
    const PLAYER_GROUPS_DELETE = 'nova.player-groups.delete';

    const CHARACTERS_VIEW = 'nova.characters.view';
    const CHARACTERS_CREATE = 'nova.characters.create';
    const CHARACTERS_UPDATE = 'nova.characters.update';
    const CHARACTERS_DELETE = 'nova.characters.delete';

    const EVENT_TYPES_VIEW = 'nova.event-types.view';
    const EVENT_TYPES_UPDATE = 'nova.event-types.update';
    const EVENT_TYPES_CREATE = 'nova.event-types.create';
    const EVENT_TYPES_DELETE = 'nova.event-types.delete';

    const SPORTS_VIEW = 'nova.sports.view';
    const SPORTS_CREATE = 'nova.sports.create';
    const SPORTS_UPDATE = 'nova.sports.update';
    const SPORTS_DELETE = 'nova.sports.delete';

    const SPORT_POSITIONS_VIEW = 'nova.sport-positions.view';
    const SPORT_POSITIONS_CREATE = 'nova.sport-positions.create';
    const SPORT_POSITIONS_UPDATE = 'nova.sport-positions.update';
    const SPORT_POSITIONS_DELETE = 'nova.sport-positions.delete';

    const STUDIOS_VIEW = 'nova.studios.view';
    const STUDIOS_CREATE = 'nova.studios.create';
    const STUDIOS_UPDATE = 'nova.studios.update';
    const STUDIOS_DELETE = 'nova.studios.delete';

    const TEAMS_VIEW = 'nova.teams.view';
    const TEAMS_CREATE = 'nova.teams.create';
    const TEAMS_UPDATE = 'nova.teams.update';
    const TEAMS_DELETE = 'nova.teams.delete';

    const PERFORMANCES_VIEW = 'nova.performances.view';
    const PERFORMANCES_CREATE = 'nova.performances.create';
    const PERFORMANCES_UPDATE = 'nova.performances.update';
    const PERFORMANCES_DELETE = 'nova.performances.delete';
}
