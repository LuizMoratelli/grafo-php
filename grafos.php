<?php
class Grafo {
    private $quantidadeVertices = 0;
    private $matrizAdjacencia = [];
    private $direcionado = false;
    private $aeroportos = [];
    private $caminhosPossiveis = [];
    private $caminhosPossiveisTratado = [];

    public function getQuantidadeVertices() {
        return $this->quantidadeVertices;
    }

    public function setQuantidadeVertices($value) {
        $this->quantidadeVertices = $value;
    }

    public function getDirecionado() {
        return $this->direcionado;
    }

    public function setDirecionado($value) {
        $this->direcionado = $value;
    }

    public function getAeroportos() {
        return $this->aeroportos;
    }

    public function setAeroportos($value) {
        $this->aeroportos = $value;
    }

    private function organizarCaminhosPossiveis($caminhosPossiveis) {
        if ($caminhosPossiveis) {
            $menorCaminhoIndice = '';
            $menorCaminhoValor = '';
            $menorCaminhoPeso = '';

            foreach ($caminhosPossiveis as $posicao => $caminho) {
                preg_match_all('!\d+!', $caminho, $menorCaminhoPesoTemporario);

                if (($menorCaminhoPesoTemporario < $menorCaminhoPeso) || (!$menorCaminhoPeso)) {
                    $menorCaminhoIndice = $posicao;
                    $menorCaminhoValor = $caminho;
                    $menorCaminhoPeso = $menorCaminhoPesoTemporario;
                }
            }

            $this->caminhosPossiveisTratado[] = $menorCaminhoValor;
            unset($caminhosPossiveis[$menorCaminhoIndice]);
            $this->organizarCaminhosPossiveis($caminhosPossiveis);
        }
    }

    public function getCaminhosPossiveis() {
        $this->organizarCaminhosPossiveis($this->caminhosPossiveis);
        return $this->caminhosPossiveisTratado;
    }

    public function setCaminhosPossiveis($value) {
        $this->caminhosPossiveis[] = $value;
    }

    // Criação de uma Matriz com todos os valores (pesos) zerados.x
    public function __construct ($vertices, $aeroportos) {
        $this->quantidadeVertices = $vertices;
        for ($i=0; $i < $this->quantidadeVertices; $i++) {
            for ($j=0; $j < $this->quantidadeVertices; $j++) { 
                $this->matrizAdjacencia[$aeroportos[$i]][$aeroportos[$j]] = 0;
            }
        }
    }

    // Altera o valor da Matriz na posição de origem e destino.
    public function adicionarAresta($origem, $destino, $peso) {
        $this->matrizAdjacencia[$origem][$destino] = $peso;

        // Se não é direcionado (o grafo), adiciona uma aresta igual e oposta
        if (!$this->direcionado) {
            $this->matrizAdjacencia[$destino][$origem] = $peso;
        }
    }

    // Verifica se o vértice já pertence ao caminhocaminho
    public function verificarVisitado($caminho, $vertice) {
        return in_array($vertice, $caminho);
    }

    // Calcula todos os possíveis caminhos
    public function verificarCaminhos($origem, $destino, $caminho = [], $pesoTotal = 0) {
        $existeCaminho = false;

        // Se ainda não tem um caminho, coloca a origem como primeiro índice do caminho
        if (!$caminho) {
            $caminho[] = $origem; 
        }

        // Define o ultimoVértice como o ultimo valor do vetor caminho
        $ultimoVertice = end($caminho);

        // Caso o ultimo valor do caminho seja igual ao destino, imprime-o
        if ($ultimoVertice == $destino) {
            return $this->salvarCaminho($caminho, $pesoTotal);   
        } else {
            for ($i=0; $i < $this->quantidadeVertices; $i++) {
                $proximoVertice = $this->matrizAdjacencia[$ultimoVertice][$this->aeroportos[$i]];

                // Caso o próximo vertice não seja 0
                if ($proximoVertice) {
                    // Caso esse vértice ainda não esteja no caminho
                    if (!$this->verificarVisitado($caminho, $this->aeroportos[$i])) {
                        $pesoAeroporto = $this->matrizAdjacencia[$ultimoVertice][$this->aeroportos[$i]];
                        $novoPesoTotal = $pesoTotal + $pesoAeroporto;
                        $existeCaminho = true;
                        // Faz um novo array juntando caminho e o novo vértice
                        $novoCaminho = array_merge($caminho, array($this->aeroportos[$i]));
                        // Função recursiva até concluir o caminho inteiro
                        $this->verificarCaminhos($origem, $destino, $novoCaminho, $novoPesoTotal);
                    }
                }
            }
            
            // Se não existe um caminho, quebrando a recursividade
            if (!$existeCaminho) {
                return false;
            }
        }

    }

    public function salvarCaminho($caminho, $pesoTotal) {
        $caminhoDescrito = "Caminho -> ".implode(' -> ', $caminho).", peso total: $pesoTotal";
        $this->setCaminhosPossiveis($caminhoDescrito);
    }
}

$aeroportos = ['MCP', 'SSA', 'PMW', 'MAO', 'GIG', 'FOR', 'CGB', 'AJU', 'BEL', 'FLN', 'GYN', 'MCZ', 'POA', 'PVH', 'JPA', 'BVB', 
               'BSB', 'PLU', 'THE', 'NAT', 'CGR', 'CGH', 'REC', 'CWB', 'SDU', 'VIX', 'RBR', 'CNF', 'GPA', 'SLZ', 'GRU'];

$vertices = 31;
$grafo = new Grafo($vertices, $aeroportos);
$grafo->setDirecionado(false);
$grafo->setAeroportos($aeroportos);
$grafo->adicionarAresta('MCP', 'SSA', 300);
$grafo->adicionarAresta('MCP', 'NAT', 300);
$grafo->adicionarAresta('SSA', 'PMW', 200);
$grafo->adicionarAresta('PMW', 'MAO', 150);
$grafo->adicionarAresta('MAO', 'GIG', 200);
$grafo->adicionarAresta('GIG', 'FOR', 200);
$grafo->adicionarAresta('FOR', 'CGB', 150);
$grafo->adicionarAresta('CGB', 'AJU', 150);
$grafo->adicionarAresta('AJU', 'BEL', 150);
$grafo->adicionarAresta('BEL', 'FLN', 300);
$grafo->adicionarAresta('BEL', 'BVB', 150);
$grafo->adicionarAresta('FLN', 'GYN', 150);
$grafo->adicionarAresta('GYN', 'MCZ', 250);
$grafo->adicionarAresta('MCZ', 'POA', 350);
$grafo->adicionarAresta('MCZ', 'BSB', 550);
$grafo->adicionarAresta('POA', 'PVH', 250);
$grafo->adicionarAresta('JPA', 'BSB', 100);
$grafo->adicionarAresta('BVB', 'BSB', 200);
$grafo->adicionarAresta('BVB', 'CGR', 200);
$grafo->adicionarAresta('BSB', 'PLU', 500);
$grafo->adicionarAresta('BSB', 'CGH', 200);
$grafo->adicionarAresta('BSB', 'REC', 300);
$grafo->adicionarAresta('PLU', 'THE', 450);
$grafo->adicionarAresta('PLU', 'CWB', 400);
$grafo->adicionarAresta('THE', 'NAT', 350);
$grafo->adicionarAresta('THE', 'VIX', 300);
$grafo->adicionarAresta('THE', 'RBR', 300);
$grafo->adicionarAresta('NAT', 'GRU', 200);
$grafo->adicionarAresta('REC', 'CWB', 100);
$grafo->adicionarAresta('CWB', 'SDU', 450);
$grafo->adicionarAresta('SDU', 'VIX', 150);
$grafo->adicionarAresta('VIX', 'RBR', 100);
$grafo->adicionarAresta('CGR', 'CNF', 200);
$grafo->adicionarAresta('CNF', 'GPA', 350);
$grafo->adicionarAresta('GPA', 'SLZ', 400);
$grafo->adicionarAresta('SLZ', 'GRU', 400);

$origem = 'JPA';
if (isset($_GET['origem'])) {
    $origem = strtoupper($_GET['origem']);
}

$destino = 'BSB';
if (isset($_GET['destino'])) {
    $destino = strtoupper($_GET['destino']);
}

$grafo->verificarCaminhos($origem, $destino);

?>

<html>
    <head>
    </head>
    </body>
        <form>
            <label for="origem">Origem</label>
            <select name="origem" id="origem">
            <?php 
                foreach ($aeroportos as $aeroporto) {
                    echo "<option value='${aeroporto}'". (($origem == $aeroporto) ? 'selected' : '').">${aeroporto}</option>";
                }
            ?>
            </select>
            <label for="destino">Destino</label>
            <select name="destino" id="destino">
            <?php 
                foreach ($aeroportos as $aeroporto) {
                    echo "<option value='${aeroporto}'". (($destino == $aeroporto) ? 'selected' : '').">${aeroporto}</option>";
                }
            ?>
            </select>
            <input type="submit" value="Gerar Rota">
        </form>
        <?php 
            $caminhos = $grafo->getCaminhosPossiveis();
            $listaCaminhos = null;
            foreach ($caminhos as $caminho) {
                $listaCaminhos .= $caminho."\n";
            }
        ?>
        <pre><?=$listaCaminhos?></pre>
        <img src="grafo.png" alt="Grafo com as ligações entre os aeroportos">
    </body>
</html>