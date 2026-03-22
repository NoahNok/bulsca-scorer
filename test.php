<?php



$t = [
    "equation" => "result==0 ? alpha_lsp : (result==1 ? beta_lsp : (result==2 ? gamma_lsp : (result==3 ? delta_lsp : (upsilon + ((time_limit-result)/(time_limit-fastest)) * (1000 - upsilon)))))",
    "global_variables" => [
        ["order" => 0, "name" => "time_limit", "expression" => 150000],
        ["order" => 0, "name" => "valid_teams", "expression" => "FILTER(results, '!disqualified')"],
        ["order" => 0, "name" => "teams_zero", "expression" => "FILTER(valid_teams, 'result==0')"],
        ["name" => "lsp", "expression" => "1000 / (COUNT(valid_teams) + 5)", "order" => 1],
        ["name" => "alpha", "expression" => "(COUNT(FILTER(valid_teams, 'result==0'))+1) * lsp", "order" => 2],
        ["name" => "beta", "expression" => "(COUNT(FILTER(valid_teams, 'result==1'))+1) * lsp", "order" => 3],
        ["name" => "gamma", "expression" => "(COUNT(FILTER(valid_teams, 'result==2'))+1) * lsp", "order" => 4],
        ["name" => "delta", "expression" => "(COUNT(FILTER(valid_teams, 'result==3'))+1) * lsp", "order" => 5],
        ["order" => 6, "name" => "alpha_lsp", "expression" => "alpha/2"],
        ["order" => 7, "name" => "beta_lsp", "expression" => "alpha + beta/2"],
        ["order" => 8, "name" => "gamma_lsp", "expression" => "alpha + beta + gamma/2"],
        ["order" => 9, "name" => "delta_lsp", "expression" => "alpha + beta + gamma + delta/2"],
        ["order" => 10, "name" => "upsilon", "expression" => "alpha + beta + gamma + delta"],
        ["order" => 11, "name" => "fastest", "expression" => "MINIMUM(FILTER(results, 'result>3'), 'result')"]
    ],

    "penalty_func" => "max(0, (result > 3 ? 4 - penalties : result - penalties))"
];
