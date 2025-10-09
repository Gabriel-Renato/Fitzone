# 📚 Guia de Uso - FitZone

## 🎯 Sistema Completo com Check-in de Exercícios

---

## 👨‍💼 PARA O PERSONAL TRAINER

### 1. Login
```
URL: http://localhost:3000/login.html
Email: personal@fitzone.com
Senha: password
```

### 2. Criar Cliente
1. No dashboard, clique em **"+ Novo Cliente"**
2. Preencha:
   - Nome completo
   - E-mail (será usado para login)
   - Senha (mínimo 6 caracteres)
   - Telefone (opcional)
   - Objetivos (ex: "Ganhar massa muscular")
3. Clique em **"Salvar"**

### 3. Ver Detalhes do Cliente
1. Clique em **"Ver Detalhes"** no card do cliente
2. Visualize:
   - ✅ Informações pessoais
   - ✅ Estatísticas (treinos realizados, avaliação média)
   - ✅ Plano semanal atual
   - ✅ Histórico dos últimos 5 treinos

### 4. Atribuir Treino ao Cliente
1. Em "Ver Detalhes", clique em **"📋 Atribuir Treino"**
2. Selecione:
   - **Treino** (ex: Treino A - Peito e Tríceps)
   - **Dia da Semana** (Segunda a Domingo)
   - **Horário** (opcional, ex: 07:00)
3. Clique em **"Atribuir Treino"**
4. ✅ O treino aparecerá no plano semanal do cliente!

### 5. Criar Novos Treinos
1. Vá para aba **"Treinos"**
2. Clique em **"+ Criar Treino"**
3. Configure:
   - Nome (ex: "Treino E - Full Body")
   - Foco (Hipertrofia/Força/Resistência/Definição)
   - Adicione exercícios da biblioteca
   - Defina séries, reps, carga e descanso
4. Clique em **"Criar Treino"**

---

## 👤 PARA O CLIENTE

### 1. Login
```
URL: http://localhost:3000/login.html
Email: joao@fitzone.com (ou maria@fitzone.com)
Senha: password
```

### 2. Ver Plano Semanal
- Dashboard mostra seu plano semanal completo
- Cards mostram: Dia, Treino, Foco, Nº de exercícios
- **Dica:** Cards com treino têm "Clique para ver exercícios 👆"

### 3. Iniciar Treino (CHECK-IN)

#### 📱 **Passo a Passo:**

1. **Clique no card do dia** (ex: Segunda-feira)
   - Abre modal com **todos os exercícios**
   
2. **Durante o treino:**
   - ✅ **Marque cada exercício** conforme realiza (clique no card ou checkbox)
   - 📊 Veja **barra de progresso** em tempo real
   - 💾 Clique em **"Salvar Check-in"** para salvar progresso parcial
   
3. **Salvar Check-in (Progresso Parcial):**
   - Útil se precisar pausar o treino
   - Salva quais exercícios já fez
   - Quando voltar, o progresso é **recuperado automaticamente**
   - Mensagem aparece: "💾 Progresso recuperado: 3 exercícios (15 minutos atrás)"

4. **Concluir Treino:**
   - Clique em **"✅ Concluir Treino"**
   - Veja **resumo dos exercícios realizados**
   - Preencha:
     - ⏱️ **Duração** (ex: 60 minutos)
     - ⭐ **Avaliação** (1-5 estrelas)
     - 📝 **Observações** (ex: "Treino puxado, mas consegui!")
   - Clique em **"Registrar"**
   - ✅ Check-in salvo é **limpo automaticamente**

### 4. Ver Histórico
- Aba **"Histórico"** mostra todos os treinos realizados
- Cada treino mostra:
  - 📅 Data
  - ⏱️ Duração
  - ⭐ Avaliação
  - 📝 Observações

### 5. Acompanhar Estatísticas
No topo do dashboard:
- **🏋️ Treinos Realizados** - Total de treinos
- **📅 Esta Semana** - Treinos desta semana
- **⭐ Avaliação Média** - Média das suas avaliações

---

## 💡 **Dicas de Uso**

### Para o Cliente:

✅ **Durante o Treino:**
- Marque exercícios conforme faz
- Use "Salvar Check-in" se precisar pausar
- Não perca seu progresso!

✅ **Após o Treino:**
- Sempre avalie com estrelas
- Adicione observações úteis (ex: "Aumentar carga no supino")
- Seu personal verá essas informações!

✅ **Acompanhamento:**
- Veja seu progresso na semana
- Compare avaliações ao longo do tempo
- Mantenha consistência!

### Para o Personal:

✅ **Gestão de Clientes:**
- Crie clientes com objetivos claros
- Atribua treinos adequados ao objetivo
- Monitore progresso no "Ver Detalhes"

✅ **Treinos:**
- Crie biblioteca variada de treinos
- Configure cargas e descansos apropriados
- Adicione observações importantes

✅ **Acompanhamento:**
- Veja quantos treinos cada cliente fez
- Confira avaliações médias
- Ajuste treinos baseado no feedback

---

## 🎬 **Fluxo Completo de Exemplo**

### Cenário: Cliente João vai treinar Segunda-feira

```
1. João faz login → Dashboard do Cliente

2. Vê card "Segunda-feira - Treino A"
   📋 7 exercícios
   👆 Clique para ver exercícios

3. Clica no card → Abre modal com exercícios:
   ☐ 1. Supino Reto - 4x8-12 - Descanso 90s
   ☐ 2. Supino Inclinado - 4x8-12
   ☐ 3. Supino Declinado - 4x8-12
   ☐ 4. Crucifixo - 3x12-15
   ☐ 5. Tríceps Testa - 4x10-12
   ☐ 6. Tríceps Pulley - 3x12-15
   ☐ 7. Tríceps Francês - 3x12-15
   
   Progresso: 0 de 7 exercícios (0%)
   [░░░░░░░░░░░░░░░░░░░░]

4. João faz Supino Reto → Marca ✅
   Progresso: 1 de 7 exercícios (14%)
   [▓▓▓░░░░░░░░░░░░░░░░░]

5. João faz mais 2 exercícios → Marca ✅✅
   Progresso: 3 de 7 exercícios (43%)
   [▓▓▓▓▓▓▓▓░░░░░░░░░░░]

6. Precisa pausar? → Clica "💾 Salvar Check-in"
   ✅ Check-in salvo! 3 exercícios marcados.

7. João volta depois → Abre o treino novamente
   💾 Progresso recuperado: 3 exercícios (2 horas atrás)
   ✅ Exercícios já marcados aparecem!

8. Completa os 4 exercícios restantes ✅✅✅✅
   Progresso: 7 de 7 exercícios (100%)
   [▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓] 🎉

9. Clica "✅ Concluir Treino"
   
   Resumo:
   7 de 7 exercícios realizados ✅
   ✅ Supino Reto - 4x8-12
   ✅ Supino Inclinado - 4x8-12
   ✅ Supino Declinado - 4x8-12
   ... (todos os exercícios)

10. Preenche:
    Duração: 65 minutos
    Avaliação: ⭐⭐⭐⭐⭐ (5 estrelas)
    Observações: "Treino excelente! Aumentar carga no supino"

11. Clica "Registrar" → Treino salvo no histórico! 🎉

12. Personal vê nos detalhes do João:
    - 1 treino realizado
    - Avaliação média: 5.0 ⭐
    - Observações do treino
```

---

## 🔑 **Diferenças Entre os Botões**

### 💾 **Salvar Check-in**
- Salva progresso **temporário**
- Não registra treino no histórico
- Permite **pausar e continuar depois**
- Dados ficam no navegador (localStorage)
- **Use quando:** Precisa pausar o treino

### ✅ **Concluir Treino**
- Registra treino **definitivamente**
- Salva no banco de dados
- Aparece no histórico
- Limpa o check-in salvo
- Conta nas estatísticas
- **Use quando:** Finalizou o treino

---

## 📊 **Dados Salvos**

### Check-in (Temporário):
```json
{
  "workoutId": 1,
  "completedExercises": [1, 2, 3],
  "savedAt": "2025-10-09T15:30:00",
  "dayName": "Segunda"
}
```

### Treino Concluído (Definitivo):
```json
{
  "workout_id": 1,
  "completed_at": "2025-10-09",
  "duration": 65,
  "rating": 5,
  "notes": "Treino excelente!",
  "exercises_completed": [1, 2, 3, 4, 5, 6, 7]
}
```

---

## 🎨 **Recursos Visuais**

✅ **Cards clicáveis** com hover effect  
✅ **Checkbox grande** e fácil de clicar  
✅ **Barra de progresso animada**  
✅ **Cores para feedback**:
   - Cinza: Não marcado
   - Verde: Marcado
   - Azul: Card em hover
✅ **Badge de progresso recuperado**  
✅ **Animações suaves**  
✅ **Responsivo** para mobile  

---

## ⚡ **Recursos Avançados**

### Auto-save:
- Progresso salvo fica disponível por 7 dias
- Mesmo fechando o navegador
- Mesmo fazendo logout

### Flexibilidade:
- Cliente pode marcar apenas alguns exercícios
- Sistema registra quais foram completados
- Personal pode ver detalhes de cada treino

### Motivação:
- Barra de progresso visual
- Mensagens de sucesso
- Sistema de estrelas para avaliação

---

## 🚀 **Pronto para Usar!**

**Acesse agora:**
```
http://localhost:3000/login.html
```

**E teste o fluxo completo! 🎉**
